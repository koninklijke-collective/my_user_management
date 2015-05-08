<?php
namespace Serfhos\MyUserManagement\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class AdministrationService
 *
 * @package Serfhos\MyUserManagement\Service
 */
class AdministrationService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Terminate BackendUser session and logout corresponding client
     * Redirects to onlineAction with message
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUser $backendUser
     * @param string $sessionId
     * @return string
     */
    public function terminateBackendUserSession(
        \Serfhos\MyUserManagement\Domain\Model\BackendUser $backendUser,
        $sessionId
    ) {
        $this->getDatabaseConnection()->exec_DELETEquery(
            'be_sessions',
            'ses_userid = "' . (int) $backendUser->getUid() . '" AND ses_id = ' . $this->getDatabaseConnection()->fullQuoteStr($sessionId,
                'be_sessions') . ' LIMIT 1'
        );
        if ($this->getDatabaseConnection()->sql_affected_rows() == 1) {
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
    public function switchUser($switchUser, $switchBack = false)
    {
        $targetUser = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('be_users', $switchUser);
        if (is_array($targetUser)) {
            // Cannot switch to admin user, when current user is not an admin!
            if ((bool) $targetUser['admin'] === true && !$this->getBackendUserAuthentication()->isAdmin()) {
                return;
            }
            $updateData['ses_userid'] = $targetUser['uid'];
            // User switchback or replace current session?
            if ($switchBack) {
                $updateData['ses_backuserid'] = (int) $GLOBALS['BE_USER']->user['uid'];
            }

            $whereClause = 'ses_id=' . $this->getDatabaseConnection()->fullQuoteStr($GLOBALS['BE_USER']->id,
                    'be_sessions');
            $whereClause .= ' AND ses_name=' . $this->getDatabaseConnection()->fullQuoteStr(BackendUserAuthentication::getCookieName(),
                    'be_sessions');
            $whereClause .= ' AND ses_userid=' . (int) $GLOBALS['BE_USER']->user['uid'];

            $this->getDatabaseConnection()->exec_UPDATEquery(
                'be_sessions',
                $whereClause,
                $updateData
            );
            $redirectUrl = $GLOBALS['BACK_PATH'] . 'index.php' . ($GLOBALS['TYPO3_CONF_VARS']['BE']['interfaces'] ? '' : '?commandLI=1');
            \TYPO3\CMS\Core\Utility\HttpUtility::redirect($redirectUrl);
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}