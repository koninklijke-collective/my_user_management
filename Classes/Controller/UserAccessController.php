<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Service\AccessService;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: UserAccess
 */
class UserAccessController extends ActionController
{

    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var \KoninklijkeCollective\MyUserManagement\Service\AccessService */
    protected $accessService;

    /**
     * Set up the doc header properly here
     *
     * @param  \TYPO3\CMS\Extbase\Mvc\View\ViewInterface  $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        if ($view instanceof BackendTemplateView) {
            $view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
            $view->getModuleTemplate()->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        }
        parent::initializeView($view);
    }

    /**
     * Action: List users
     *
     * @return void
     */
    public function indexAction()
    {
        $pageId = (int)GeneralUtility::_GP('id');
        $backendUsers = $this->getAccessService()->findUsersWithPageAccess($pageId);

        if ($pageId === 0) {
            $this->addFlashMessage(
                $this->translate('no_selection_description'),
                $this->translate('no_selection_title'),
                AbstractMessage::NOTICE
            );
        } elseif (count($backendUsers) === 0) {
            $this->addFlashMessage(
                $this->translate('empty_description', [$pageId]),
                $this->translate('empty_title', [$pageId]),
                AbstractMessage::INFO
            );
        } else {
            $this->view->assignMultiple([
                'pageId' => $pageId,
                'backendUsers' => $backendUsers,
                'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            ]);
        }
    }

    /**
     * Translate label for module
     *
     * @param  string  $key
     * @param  array  $arguments
     * @return string
     */
    protected function translate($key, $arguments = [])
    {
        $label = null;
        if (!empty($key)) {
            $label = LocalizationUtility::translate(
                'backendUserAccessOverview_' . $key,
                'my_user_management',
                $arguments
            );
        }

        return ($label) ? $label : $key;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\AccessService
     */
    protected function getAccessService()
    {
        if ($this->accessService === null) {
            $this->accessService = $this->objectManager->get(AccessService::class);
        }

        return $this->accessService;
    }
}
