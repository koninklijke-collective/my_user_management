<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: BackendUser
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class BackendUserController extends \TYPO3\CMS\Beuser\Controller\BackendUserController
{

    /**
     * Override generic backend user repository
     *
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * Displays all BackendUsers
     * - Switch session to different user
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return void
     */
    public function indexAction(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand = null)
    {
        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            if ($demand === null) {
                $demand = $this->moduleData->getDemand();
            } elseif ($demand->getUserType() !== DEMAND::USERTYPE_USERONLY) {
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
        $this->view->assign(
            'returnUrl',
            rawurlencode(BackendUtility::getModuleUrl('MyUserManagementMyusermanagement_MyUserManagementUseradmin'))
        );
    }

    /**
     * Switches to a given user (SU-mode) and then redirects to the start page of the backend to refresh the navigation etc.
     *
     * @param string $switchUser BE-user record that will be switched to
     * @return void
     */
    protected function switchUser($switchUser)
    {
        $targetUser = BackendUtility::getRecord('be_users', $switchUser);
        if (is_array($targetUser)) {
            // Cannot switch to admin user, when current user is not an admin!
            if ((bool) $targetUser['admin'] === true && !$this->getBackendUserAuthentication()->isAdmin()) {
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
        }
    }

    /**
     * Translate label for module
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate($key, $arguments = array())
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
}