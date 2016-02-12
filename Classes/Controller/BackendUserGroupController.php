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
            rawurlencode(BackendUtility::getModuleUrl('myusermanagement_MyUserManagementUseradmin',
                array(
                    'tx_myusermanagement_myusermanagement_myusermanagementuseradmin' => array(
                        'action' => 'index',
                        'controller' => 'BackendUserGroup'
                    )
                )))
        );
    }
}