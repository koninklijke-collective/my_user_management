<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: FileMount
 */
class FileMountController extends ActionController
{

    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository */
    protected $fileMountRepository;

    /**
     * Set up the doc header properly here
     *
     * @param  \TYPO3\CMS\Extbase\Mvc\View\ViewInterface  $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        if ($view instanceof BackendTemplateView) {
            $this->registerDocheaderButtons();
            $view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
            $view->getModuleTemplate()->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        }
        parent::initializeView($view);
    }

    /**
     * Registers the Icons into the docheader
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function registerDocheaderButtons()
    {
        /** @var \TYPO3\CMS\Backend\Template\Components\ButtonBar $buttonBar */
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        if ($this->request->getControllerName() === 'FileMount') {
            if ($this->request->getControllerActionName() === 'index') {
                $returnUrl = rawurlencode(BackendUtility::getModuleUrl('myusermanagement_MyUserManagementFilemountadmin'));
                $parameters = GeneralUtility::explodeUrl2Array('edit[sys_filemounts][0]=new&returnUrl=' . $returnUrl);
                $addUserLink = BackendUtility::getModuleUrl('record_edit', $parameters);
                $title = $this->translate('file_mount_add_new');
                $icon = $this->view->getModuleTemplate()
                    ->getIconFactory()
                    ->getIcon('actions-document-new', Icon::SIZE_SMALL);
                $addUserButton = $buttonBar->makeLinkButton()
                    ->setHref($addUserLink)
                    ->setTitle($title)
                    ->setIcon($icon);
                $buttonBar->addButton($addUserButton, ButtonBar::BUTTON_POSITION_LEFT);
            }
        }
    }

    /**
     * Action: List all file mounts
     *
     * @return void
     */
    public function indexAction()
    {
        if (AccessUtility::beUserHasRightToEditTable(FileMount::TABLE) === false) {
            $this->addFlashMessage(
                $this->translate('access_users_table_not_allowed_description', [FileMount::TABLE]),
                $this->translate('access_users_table_not_allowed_title'),
                AbstractMessage::ERROR
            );
        }

        $this->view->assign('returnUrl',
            BackendUtility::getModuleUrl('myusermanagement_MyUserManagementFilemountadmin'));

        $fileMounts = $this->getFileMountRepository()->findAll();
        if (count($fileMounts) === 0) {
            $this->addFlashMessage(
                $this->translate('empty_description'),
                $this->translate('empty_title'),
                AbstractMessage::INFO
            );
        } else {
            $this->view->assign('fileMounts', $fileMounts);
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
                'backendFileMountOverview_' . $key,
                'my_user_management',
                $arguments
            );
        }

        return ($label) ? $label : $key;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository
     */
    protected function getFileMountRepository()
    {
        if ($this->fileMountRepository === null) {
            $this->fileMountRepository = $this->objectManager->get(FileMountRepository::class);
        }

        return $this->fileMountRepository;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
