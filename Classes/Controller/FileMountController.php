<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: FileMount
 *
 * @todo check for refactoring
 */
final class FileMountController extends ActionController
{
    use TranslateTrait;

    private BackendUriBuilder $backendUriBuilder;
    private IconFactory $iconFactory;
    private ModuleTemplateFactory $moduleTemplateFactory;
    private FileMountRepository $fileMountRepository;
    private ?ModuleTemplate $moduleTemplate = null;

    public function __construct(
        FileMountRepository $fileMountRepository,
        ModuleTemplateFactory $moduleTemplateFactory,
        BackendUriBuilder $backendUriBuilder,
        IconFactory $iconFactory
    )
    {
        $this->fileMountRepository = $fileMountRepository;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->backendUriBuilder = $backendUriBuilder;
        $this->iconFactory = $iconFactory;
    }

    /**
     * Init module state.
     * This isn't done within __construct() since the controller
     * object is only created once in extbase when multiple actions are called in
     * one call. When those change module state, the second action would see old state.
     */
    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:my_user_management/Resources/Private/Language/Backend/FileMount.xlf:mlang_tabs_tab'));
    }

    public function indexAction(int $currentPage = 1): ResponseInterface
    {
        if (!AccessUtility::beUserHasRightToEditTable(FileMount::TABLE)) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUserGroup::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        $fileMounts = $this->fileMountRepository->findAll();
        $paginator = new QueryResultPaginator($fileMounts, $currentPage, 50);
        $pagination = new SimplePagination($paginator);
        $this->view->assignMultiple(
            [
                'fileMounts' => $fileMounts,
                'paginator' => $paginator,
                'pagination' => $pagination,
            ]
        );
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $addFilemountButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL))
            ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newRecordGeneral'))
            ->setHref(
                $this->backendUriBuilder->buildUriFromRoute('record_edit', [
                    'edit' => ['sys_filemounts' => [0 => 'new']],
                    'returnUrl' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                ])
            );
        $buttonBar->addButton($addFilemountButton);
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementFilemountadmin')
            ->setDisplayName(LocalizationUtility::translate('myUserManagementFilemountadmin', 'myUserManagement'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }
}
