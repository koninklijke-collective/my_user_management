<?php
namespace KoninklijkeCollective\MyUserManagement\Service;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service: Access Look up
 *
 * @package KoninklijkeCollective\MyUserManagement\Service
 */
class AccessService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserRepository
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
        $rootLineIds = [];
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
    public function findAllBackendUsers()
    {
        $returnedUsers = [];
        $users = $this->backendUserRepository->findAllActive();

        foreach ($users as $user) {
            if ($user instanceof BackendUser) {
                if ($this->isAllowedUser($user) === false) {
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
     * Find all inactive backend users
     *
     * @return array
     */
    public function findAllInactiveBackendUsers()
    {
        $returnedUsers = [];
        $loginSince = new \DateTime('- 6 months');
        $users = $this->backendUserRepository->findAllInactive($loginSince);

        foreach ($users as $user) {
            if ($user instanceof BackendUser) {
                if ($this->isAllowedUser($user) === false) {
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
     * Find backend user
     *
     * @param integer $userId
     * @return array
     */
    public function findBackendUser($userId)
    {
        $user = $this->backendUserRepository->findByUid((int) $userId);

        if ($user instanceof BackendUser) {
            if ($this->isAllowedUser($user) === false) {
                return null;
            }

            $mounts = $user->getDbMountPoints();
            foreach ($user->getBackendUserGroups() as $group) {
                $mounts = $this->getAllDatabaseMountsFromUserGroup($group, $mounts);
            }
            $user->setInheritedMountPoints($mounts);
        }

        return $user;
    }

    /**
     * Check if given user is allowed for current logged in user
     *
     * @param BackendUser $user
     * @return boolean
     */
    protected function isAllowedUser(BackendUser $user)
    {
        if ($user->getIsDisabled() === true) {
            return false;
        } elseif ($this->getBackendUserAuthentication()->isAdmin() === false && $user->getIsAdministrator() === true) {
            // Ignore admins if a non admin is retrieving the information!
            return false;
        } elseif (GeneralUtility::isFirstPartOfStr($user->getUserName(), '_cli_')) {
            // Ignore _cli users
            return false;
        }

        return true;
    }

    /**
     * @param \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $group
     * @param array $mounts
     * @return array
     */
    protected function getAllDatabaseMountsFromUserGroup(BackendUserGroup $group, array $mounts = [])
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
        $returnedUsers = [];
        $users = $this->findAllBackendUsers();
        foreach ($users as $user) {
            if ($user instanceof BackendUser) {
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
     * Generate menu for non-admin views
     *
     * @param \TYPO3\CMS\Backend\Template\Components\MenuRegistry $menuRegistry
     * @param \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @return bool
     */
    public function generateMenu(
        \TYPO3\CMS\Backend\Template\Components\MenuRegistry $menuRegistry,
        \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder,
        \TYPO3\CMS\Extbase\Mvc\Request $request
    ) {
        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $menuItems = [];
            if (AccessUtility::beUserHasRightToEditTable(BackendUser::TABLE)) {
                $menuItems['index'] = [
                    'controller' => 'BackendUser',
                    'action' => 'index',
                    'label' => $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUsers')
                ];
            }

            if (AccessUtility::beUserHasRightToEditTable(BackendUserGroup::TABLE)) {
                $menuItems['pages'] = [
                    'controller' => 'BackendUserGroup',
                    'action' => 'index',
                    'label' => $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUserGroupsMenu')
                ];
            }
            if (!empty($menuItems)) {
                $uriBuilder->setRequest($request);

                $menu = $menuRegistry->makeMenu();
                $menu->setIdentifier('BackendUserModuleMenu');

                foreach ($menuItems as $menuItemConfig) {
                    if ($request->getControllerName() === $menuItemConfig['controller']) {
                        $isActive = $request->getControllerActionName() === $menuItemConfig['action'] ? true : false;
                    } else {
                        $isActive = false;
                    }
                    $menuItem = $menu->makeMenuItem()
                        ->setTitle($menuItemConfig['label'])
                        ->setHref($uriBuilder->reset()->uriFor($menuItemConfig['action'], [], $menuItemConfig['controller']))
                        ->setActive($isActive);
                    $menu->addMenuItem($menuItem);
                }

                $menuRegistry->addMenu($menu);
            }

            return true;
        }
        return false;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
