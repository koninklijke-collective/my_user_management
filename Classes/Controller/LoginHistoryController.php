<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: LoginHistory
 */
class LoginHistoryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var \KoninklijkeCollective\MyUserManagement\Service\LogService */
    protected $logService;

    /** @var \KoninklijkeCollective\MyUserManagement\Service\AccessService */
    protected $accessService;

    /**
     * Set up the doc header properly here
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        if ($view instanceof BackendTemplateView) {
            $view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
            $view->getModuleTemplate()->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        }

        parent::initializeView($view);
        $view->assignMultiple([
            'returnUrl' => BackendUtility::getModuleUrl('myusermanagement_MyUserManagementLoginhistory'),
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    /**
     * Action: List all users
     *
     * @param integer $page
     * @return void
     */
    public function indexAction($page = 1)
    {
        $parameters = [
            'page' => $page,
            'itemsPerPage' => 20,
            'hide-admin' => ($this->getBackendUserAuthentication()->isAdmin() === false),
        ];
        $logs = $this->getLogService()->findUserLoginActions($parameters);
        if (empty($logs['items'])) {
            $this->addFlashMessage(
                $this->translate('empty_description'),
                $this->translate('empty_title'),
                AbstractMessage::INFO
            );
        }

        $this->view->assignMultiple([
            'backendUsers' => $this->getAccessService()->findAllBackendUsers(),
            'inactiveUsers' => $this->getAccessService()->findAllInactiveBackendUsers(),
            'userModuleAccess' => AccessUtility::beUserHasRightToSeeModule('myusermanagement_MyUserManagementUseradmin') && AccessUtility::beUserHasRightToEditTable(BackendUser::TABLE),
            'logs' => $logs,
        ]);
    }

    /**
     * Action: Get login overview
     *
     * @param integer $user
     * @return void
     */
    public function detailAction($user = null)
    {
        if ($user === null) {
            $this->redirect('index');
        }

        $user = $this->getAccessService()->findBackendUser($user);
        if ($user instanceof BackendUser) {
            $parameters = [
                'user' => $user->getUid(),
            ];
            $logs = $this->getLogService()->findUserLoginActions($parameters);

            $this->view->assignMultiple([
                'user' => $user,
                'logs' => $logs,
            ]);
        } else {
            $this->redirect('index');
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
                'backendLoginHistory_' . $key,
                'my_user_management',
                $arguments
            );
        }
        return ($label) ? $label : $key;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\LogService
     */
    protected function getLogService()
    {
        if ($this->logService === null) {
            $this->logService = $this->objectManager->get('KoninklijkeCollective\MyUserManagement\Service\LogService');
        }
        return $this->logService;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\AccessService
     */
    protected function getAccessService()
    {
        if ($this->accessService === null) {
            $this->accessService = $this->objectManager->get('KoninklijkeCollective\MyUserManagement\Service\AccessService');
        }
        return $this->accessService;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
