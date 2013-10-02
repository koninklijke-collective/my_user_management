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
 * Class AdministrationService
 *
 * @package Serfhos\MyUserManagement\Service
 */
class AdministrationService implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $database;

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $backEndUser;

    /**
     * Initialize used variables
     */
    public function __construct() {
        $this->database = $GLOBALS['TYPO3_DB'];
        $this->backEndUser = $GLOBALS['BE_USER'];
    }

    /**
     * Terminate BackendUser session and logout corresponding client
     * Redirects to onlineAction with message
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUser $backendUser
     * @param string $sessionId
     * @return string
     */
    public function terminateBackendUserSession(\Serfhos\MyUserManagement\Domain\Model\BackendUser $backendUser, $sessionId) {
        $this->database->exec_DELETEquery(
            'be_sessions',
            'ses_userid = "' . intval($backendUser->getUid()) . '" AND ses_id = ' . $this->database->fullQuoteStr($sessionId, 'be_sessions') . ' LIMIT 1'
        );
        if ($this->database->sql_affected_rows() == 1) {
            return 'Session successfully terminated.';
        }
        return '';
    }

    /**
     * Switches to a given user (SU-mode) and then redirects to the start page of the backend to refresh the navigation etc.
     *
     * @param string $switchUser BE-user record that will be switched to
     * @param boolean $switchBack
     * @return void
     */
    public function switchUser($switchUser, $switchBack = FALSE) {
        $targetUser = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('be_users', $switchUser);
        if (is_array($targetUser)) {
            // Cannot switch to user, when current user is not an admin!
            if ((bool) $targetUser['admin'] === TRUE && !$this->backEndUser->isAdmin()) {
                return;
            }
            $updateData['ses_userid'] = $targetUser['uid'];
            // User switchback or replace current session?
            if ($switchBack) {
                $updateData['ses_backuserid'] = intval($GLOBALS['BE_USER']->user['uid']);
            }

            $whereClause = 'ses_id=' . $this->database->fullQuoteStr($GLOBALS['BE_USER']->id, 'be_sessions');
            $whereClause .= ' AND ses_name=' . $this->database->fullQuoteStr(\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::getCookieName(), 'be_sessions');
            $whereClause .= ' AND ses_userid=' . intval($GLOBALS['BE_USER']->user['uid']);

            $this->database->exec_UPDATEquery(
                'be_sessions',
                $whereClause,
                $updateData
            );
            $redirectUrl = $GLOBALS['BACK_PATH'] . 'index.php' . ($GLOBALS['TYPO3_CONF_VARS']['BE']['interfaces'] ? '' : '?commandLI=1');
            \TYPO3\CMS\Core\Utility\HttpUtility::redirect($redirectUrl);
        }
    }

}
?>