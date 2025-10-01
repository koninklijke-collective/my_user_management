<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[Controller]
final class UserAccessController extends ActionController
{
    use TranslateTrait;
    use BackendUserAuthenticationTrait;

    private ModuleTemplate $moduleTemplate;

    public function __construct(
        private readonly BackendUserService $backendUserService,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
    ) {
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
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());

        $this->moduleTemplate->assignMultiple([
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    public function indexAction(): ResponseInterface
    {
        $page = $this->getSelectedPage($this->request);
        if ($page === null) {
            return $this->moduleTemplate->renderResponse('UserAccess/NoPageSelected');
        }

        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_user_access')
            ->setArguments(['id' => $page['uid']])
            ->setDisplayName(LocalizationUtility::translate('myusermanagement_user_access', 'myUserManagement') . ' - ' . $page['uid']);
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        $this->moduleTemplate->assignMultiple([
            'page' => $page,
            'backendUsers' => $this->backendUserService->findUsersWithPageAccess($page['uid']),
        ]);

        return $this->moduleTemplate->renderResponse('UserAccess/Overview');
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
