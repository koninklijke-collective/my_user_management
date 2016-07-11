<?php
namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: BackendUser
 *
 * @package KoninklijkeCollective\MyUserManagement\Controller
 */
class BackendUserController extends \TYPO3\CMS\Beuser\Controller\BackendUserController
{

    /**
     * Override generic backend user repository
     *
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Service\OverrideService
     */
    protected $overrideService;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

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
            $_view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\TemplateView::class);
            $this->setViewConfiguration($_view);
            $view->injectTemplateView($_view);
        } else {
            parent::setViewConfiguration($view);
        }
    }

    /**
     * Override menu generation for non-admin views
     *
     * @return void
     */
    protected function generateMenu()
    {
        $this->getOverrideService()->generateMenu(
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry(),
            $this->uriBuilder->reset(),
            $this->request
        );
    }

    /**
     * Override button generation for non-admin views
     *
     * @return void
     */
    protected function registerDocheaderButtons()
    {
        $this->getOverrideService()->registerDocheaderButtons(
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar(),
            $this->request,
            $this->view->getModuleTemplate()->getIconFactory()
        );
    }

    /**
     * Displays all BackendUsers
     * - Switch session to different user
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return void
     */
    public function indexAction(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand = null)
    {
        if (AccessUtility::beUserHasRightToEditTable(BackendUser::TABLE) === false) {
            $this->addFlashMessage(
                $this->translate('access_users_table_not_allowed_description', [BackendUser::TABLE]),
                $this->translate('access_users_table_not_allowed_title'),
                AbstractMessage::ERROR
            );
        }

        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            if ($demand === null) {
                $demand = $this->moduleData->getDemand();
            } elseif ($demand->getUserType() !== Demand::USERTYPE_USERONLY) {
                $this->addFlashMessage(
                    $this->translate('display_admin_not_allowed_description'),
                    $this->translate('display_admin_not_allowed_title'),
                    AbstractMessage::ERROR
                );
            }

            $demand->setUserType(Demand::USERTYPE_USERONLY);
            $this->moduleData->setDemand($demand);
        }

        parent::indexAction($demand);
        $this->view->assign('returnUrl', BackendUtility::getModuleUrl('myusermanagement_MyUserManagementUseradmin'));
    }

    /**
     * Switches to a given user (SU-mode) and then redirects to the start page of the backend to refresh the navigation etc.
     *
     * @param string $switchUser BE-user record that will be switched to
     * @return void
     */
    protected function switchUser($switchUser)
    {
        if ($this->getBackendUserAuthentication()->isAdmin()) {
            parent::switchUser($switchUser);
        } else {
            $targetUser = BackendUtility::getRecord('be_users', $switchUser);
            if (is_array($targetUser)) {
                // Cannot switch to admin user, when current user is not an admin!
                if ((bool) $targetUser['admin'] === true) {
                    $this->addFlashMessage(
                        $this->translate('admin_switch_not_allowed_description'),
                        $this->translate('admin_switch_not_allowed_title'),
                        AbstractMessage::ERROR
                    );

                    return;
                }

                // If all is successful, simulate admin functionality
                $this->getBackendUserAuthentication()->user['admin'] = 1;
                parent::switchUser($switchUser);
            } else {
                $this->addFlashMessage(
                    $this->translate('switch_user_not_found_description'),
                    $this->translate('switch_user_not_found_title'),
                    AbstractMessage::ERROR
                );

                return;
            }
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
            $label = LocalizationUtility::translate(
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
     * @return \KoninklijkeCollective\MyUserManagement\Service\OverrideService
     */
    protected function getOverrideService()
    {
        if ($this->overrideService === null) {
            $this->overrideService = $this->objectManager->get(\KoninklijkeCollective\MyUserManagement\Service\OverrideService::class);
        }
        return $this->overrideService;
    }
}
