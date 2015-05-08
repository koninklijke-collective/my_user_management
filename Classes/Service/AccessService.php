<?php
namespace Serfhos\MyUserManagement\Service;

use Serfhos\MyUserManagement\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class AccessService
 *
 * @package Serfhos\MyUserManagement\Service
 */
class AccessService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * Find users which has access to given page id
     * Checks db mounts
     *
     * @param integer $page
     * @return array
     */
    public function findUsersWithPageAccess($page)
    {
        $rootLine = BackendUtility::BEgetRootLine($page);
        $rootLineIds = array();
        foreach ($rootLine as $page) {
            $rootLineIds[] = (int) $page['uid'];
        }
        $users = $this->findAllowedUsersInRootLine($rootLineIds);
        return $users;
    }

    /**
     * Find all known backend users
     *
     * @return array
     */
    protected function findAllBackendUsers()
    {
        $returnedUsers = array();
        $users = $this->backendUserRepository->findAll();

        foreach ($users as $user) {
            if ($user instanceof \Serfhos\MyUserManagement\Domain\Model\BackendUser) {
                if ($this->getBackendUserAuthentication()->isAdmin() === false && $user->getIsAdministrator()) {
                    // Ignore admins if a non admin is retrieving the information!
                    continue;
                }

                $mounts = $user->getDbMountPoints();
                foreach ($user->getBackendUserGroups() as $group) {
                    $mounts = $this->getAllDatabaseMountsFromUserGroup($group, $mounts);
                }
                $user->setInheritedMountPoints($mounts);

                $returnedUsers[] = $user;
            }
        }
        return $returnedUsers;
    }

    /**
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroup $group
     * @param array $mounts
     * @return array
     */
    protected function getAllDatabaseMountsFromUserGroup(BackendUserGroup $group, array $mounts = array())
    {
        $dbMounts = $group->getDbMountPoints();
        if (is_array($dbMounts)) {
            $mounts = array_unique(array_merge($mounts, $dbMounts));
        }

        foreach ($group->getSubGroups() as $subGroup) {
            $mounts = $this->getAllDatabaseMountsFromUserGroup($subGroup, $mounts);
        }
        return $mounts;
    }

    /**
     * Find users that has access in rootline
     *
     * @param array $rootLine
     * @return array
     */
    protected function findAllowedUsersInRootLine($rootLine)
    {
        $returnedUsers = array();
        $users = $this->findAllBackendUsers();
        foreach ($users as $user) {
            if ($user instanceof \Serfhos\MyUserManagement\Domain\Model\BackendUser) {
                if ($user->getIsAdministrator()) {
                    $returnedUsers[] = $user;
                } else {
                    if (count($match = array_intersect($rootLine, $user->getInheritedMountPoints())) > 0) {
                        $user->setActiveMountPoints($match);
                        $returnedUsers[] = $user;
                    }
                }
            }
        }
        return $returnedUsers;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}