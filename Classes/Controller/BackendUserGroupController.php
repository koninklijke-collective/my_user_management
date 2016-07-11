<?php
namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Controller: BackendUserGroup
 *
 * @package KoninklijkeCollective\MyUserManagement\Controller
 */
class BackendUserGroupController extends \TYPO3\CMS\Beuser\Controller\BackendUserGroupController
{

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Service\AccessService
     */
    protected $accessService;

    /**
     * Override menu generation for non-admin views
     *
     * @return void
     */
    protected function generateMenu()
    {
        if ($this->getAccessService()->generateMenu(
                $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry(),
                $this->uriBuilder->reset(),
                $this->request
            ) === false
        ) {
            parent::generateMenu();
        }
    }

    /**
     * Displays all BackendUserGroups
     *
     * @return void
     */
    public function indexAction()
    {
        if (AccessUtility::beUserHasRightToEditTable(BackendUserGroup::TABLE) === false) {
            $this->addFlashMessage(
                $this->translate('access_users_table_not_allowed_description', [BackendUserGroup::TABLE]),
                $this->translate('access_users_table_not_allowed_title'),
                AbstractMessage::ERROR
            );
        }

        parent::indexAction();
        $this->view->assign(
            'returnUrl',
            BackendUtility::getModuleUrl('myusermanagement_MyUserManagementUseradmin',
                [
                    'tx_myusermanagement_myusermanagement_myusermanagementuseradmin' => [
                        'action' => 'index',
                        'controller' => 'BackendUserGroup'
                    ]
                ])
        );
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
     * Translate label for module
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate($key, $arguments = [])
    {
        $label = null;
        if (!empty($key)) {
            $label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'backendUserAdminOverview_' . $key,
                'my_user_management',
                $arguments
            );
        }
        return ($label) ? $label : $key;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\AccessService
     */
    protected function getAccessService()
    {
        if ($this->accessService === null) {
            $this->accessService = $this->objectManager->get(\KoninklijkeCollective\MyUserManagement\Service\AccessService::class);
        }
        return $this->accessService;
    }

}
