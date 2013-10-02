<?php
namespace Serfhos\MyUserManagement\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Benjamin Serfhos <serfhos@serfhos.com>,
 *  Rotterdam School of Management, Erasmus University
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class AccessService
 *
 * @package Serfhos\MyUserManagement\Service
 */
class AccessService implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $database;

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $currentUser;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * Cached Backend Users
     *
     * @var array
     */
    protected $_backendUsers = array();

    /**
     * Initialize used variables
     */
    public function __construct() {
        $this->database = $GLOBALS['TYPO3_DB'];
        $this->currentUser = $GLOBALS['BE_USER'];
    }

    /**
     * Find users which has access to given page id
     * Checks db mounts
     *
     * @param integer $page
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return array
     */
    public function findUsersWithPageAccess($page, \TYPO3\CMS\Beuser\Domain\Model\Demand $demand) {
        $rootLine = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($page);
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
    protected function findAllBackendUsers(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand) {
        $returnedUsers = array();
        if (empty($this->_backendUsers)) {
            $users = $this->backendUserRepository->findDemanded($demand);

            foreach ($users as $user) {
                /** @var \Serfhos\MyUserManagement\Domain\Model\BackendUser $user */
                if ($this->currentUser->isAdmin() === FALSE && $user->getIsAdministrator()) {
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
        } else {
            $returnedUsers = $this->_backendUsers;
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
    protected function findAllowedUsersInRootLine($rootLine, \TYPO3\CMS\Beuser\Domain\Model\Demand $demand) {
        $returnedUsers = array();
        $users = $this->findAllBackendUsers($demand);
        foreach ($users as $user) {
            if ($user->getIsAdministrator()) {
                $returnedUsers[] = $user;
            } else {
                if (count($match = array_intersect($rootLine, $user->getInheritedMountPoints())) > 0) {
                    $user->setActiveMountPoints($match);
                    $returnedUsers[] = $user;
                }
            }
        }
        return $returnedUsers;
    }

}
?>