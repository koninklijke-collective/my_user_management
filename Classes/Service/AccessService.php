<?php
namespace Serfhos\MyUserManagement\Service;

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
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return array
     */
    public function findUsersWithPageAccess($page, \TYPO3\CMS\Beuser\Domain\Model\Demand $demand)
    {
        $rootLine = BackendUtility::BEgetRootLine($page);
        $rootLineIds = array();
        foreach ($rootLine as $page) {
            $rootLineIds[] = (int) $page['uid'];
        }
        $users = $this->findAllowedUsersInRootLine($rootLineIds, $demand);
        return $users;
    }

    /**
     * Find all known backend users
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return array
     */
    protected function findAllBackendUsers(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand)
    {
        $returnedUsers = array();
        $users = $this->backendUserRepository->findDemanded($demand);

        foreach ($users as $user) {
            if ($user instanceof \Serfhos\MyUserManagement\Domain\Model\BackendUser) {
                if ($this->getBackendUserAuthentication()->isAdmin() === false && $user->getIsAdministrator()) {
                    // Ignore admins if a non admin is retrieving the information!
                    continue;
                }

                $dbMounts = $user->getDbMountPoints();
                foreach ($user->getBackendUserGroups() as $group) {
                    $_dbMounts = $group->getDbMountPoints();
                    if (is_array($_dbMounts)) {
                        $dbMounts = array_merge($dbMounts, $_dbMounts);
                    }
                }
                $user->setInheritedMountPoints($dbMounts);

                $returnedUsers[] = $user;
            }
        }

        return $returnedUsers;
    }

    /**
     * Find users that has access in rootline
     *
     * @param array $rootLine
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return array
     */
    protected function findAllowedUsersInRootLine($rootLine, \TYPO3\CMS\Beuser\Domain\Model\Demand $demand)
    {
        $returnedUsers = array();
        $users = $this->findAllBackendUsers($demand);
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