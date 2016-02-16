<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Controller: BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class BackendUserGroupController extends \TYPO3\CMS\Beuser\Controller\BackendUserGroupController
{

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     */
    protected $backendUserGroupRepository;

    /**
     * @param \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository $backendUserGroupRepository
     */
    public function injectBackendUserGroupRepository(\Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository $backendUserGroupRepository)
    {
        $this->backendUserGroupRepository = $backendUserGroupRepository;
    }

    /**
     * Set up the view template configuration correctly for BackendTemplateView
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function setViewConfiguration(ViewInterface $view)
    {
        if (class_exists('\TYPO3\CMS\Backend\View\BackendTemplateView') && ($view instanceof \TYPO3\CMS\Backend\View\BackendTemplateView)) {
            /** @var \TYPO3\CMS\Fluid\View\TemplateView $_view */
            $_view = $this->objectManager->get('TYPO3\CMS\Fluid\View\TemplateView');
            $this->setViewConfiguration($_view);
            $view->injectTemplateView($_view);
        } else {
            parent::setViewConfiguration($view);
        }
    }

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