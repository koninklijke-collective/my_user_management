<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: User Access
 */
final class UserAccessController extends ActionController
{
    use TranslateTrait;
    use BackendUserAuthenticationTrait;

    private ModuleTemplateFactory $moduleTemplateFactory;
    private BackendUserService $backendUserService;
    private \TYPO3\CMS\Backend\Template\ModuleTemplate $moduleTemplate;


    public function __construct(
        BackendUserService $backendUserService,
        ModuleTemplateFactory $moduleTemplateFactory
    ) {
        $this->backendUserService = $backendUserService;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
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
        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAccess.xlf:mlang_tabs_tab'));
    }


    public function indexAction(): ResponseInterface
    {
        $this->view->assignMultiple(
            [
                'shortcutLabel' => 'MyUserAccess',
                'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            ]
        );
        $page = $this->getSelectedPage($this->request);
        if ($page === null) {
            $this->addFlashMessage(
                self::translate('no_page_selected_description'),
                self::translate('no_page_selected_title'),
                AbstractMessage::WARNING
            );
        } else {
            $this->view->assignMultiple(
                [
                    'page' => $page,
                    'backendUsers' => $this->backendUserService->findUsersWithPageAccess($page['uid']),
                ]
            );
        }
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementUseraccess')
            ->setArguments(['id' => $page['uid']])
            ->setDisplayName(LocalizationUtility::translate('myusermanagement_MyUserManagementUseraccess', 'myUserManagement') . ' - ' . $page['uid']);
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }


    private function getSelectedPage(ServerRequestInterface $request): ?array
    {
        $pageId = (int)($request->getParsedBody()['id'] ?? $request->getQueryParams()['id'] ?? 0);
        if ($pageId === 0) {
            return null;
        }

        $page = BackendUtility::readPageAccess(
            $pageId,
            self::getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW)
        );

        return is_array($page) ? $page : null;
    }
}
