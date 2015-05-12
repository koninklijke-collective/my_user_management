<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Controller: BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class BackendUserGroupController extends \TYPO3\CMS\Beuser\Controller\BackendUserGroupController
{

    /**
     * Displays all BackendUserGroups
     *
     * @return void
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->assign(
            'returnUrl',
            rawurlencode(BackendUtility::getModuleUrl('MyUserManagementMyusermanagement_MyUserManagementUseradmin',
                array(
                    'tx_myusermanagement_myusermanagementmyusermanagement_myusermanagementuseradmin' => array(
                        'action' => 'index',
                        'controller' => 'BackendUserGroup'
                    )
                )))
        );
    }
}