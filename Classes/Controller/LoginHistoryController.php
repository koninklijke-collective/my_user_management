<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: User Login History
 */
final class LoginHistoryController extends ActionController
{
    use TranslateTrait;

    private BackendUriBuilder $backendUriBuilder;
    private IconFactory $iconFactory;
    private ModuleTemplateFactory $moduleTemplateFactory;
    private LoginHistoryRepository $loginHistoryRepository;
    private BackendUserService $backendUserService;
    private OnlineSessionService $onlineSessionService;
    private ModuleTemplate $moduleTemplate;

    public function __construct(
        LoginHistoryRepository $loginHistoryRepository,
        BackendUserService $backendUserService,
        ModuleTemplateFactory $moduleTemplateFactory,
        OnlineSessionService $onlineSessionService,
        BackendUriBuilder $backendUriBuilder,
        IconFactory $iconFactory,
    ) {
        $this->loginHistoryRepository = $loginHistoryRepository;
        $this->backendUserService = $backendUserService;
        $this->onlineSessionService = $onlineSessionService;
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

    public function indexAction(): ResponseInterface
    {
        $this->initializeDefaultViewVariables();
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementLoginhistory')
            ->setDisplayName(LocalizationUtility::translate('myUserManagementLoginhistory', 'myUserManagement'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        $loginSince = new DateTime($this->settings['since'] ?? '- 6 months');
        $this->view->assignMultiple(
            [
                'onlineBackendUsers' => $this->onlineSessionService->getSessions(),
                'backendUsers' => $this->backendUserService->findAllBackendUsersForDropdown(),
                'loginHistory' => $this->loginHistoryRepository->lastLoggedInUsers(),
                'inactiveUsers' => $this->backendUserService->findAllInactiveBackendUsers($loginSince),
            ]
        );
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * Action: Get login overview
     *
     * @param int |null $user
     * @return void
     */
    public function detailAction(?int $user = null): ResponseInterface
    {
        $this->initializeDefaultViewVariables();
        // Forward POST request to GET for correct workflow
        if ($this->request instanceof Request && $this->request->getMethod() === 'POST') {
            $this->redirect('detail', null, null, ['user' => $user]);
        }

        if ($user === null) {
            // throws StopActionException - in v12 needs to return Response
            $this->redirect('index');
        }

        $backendUser = $this->backendUserService->findBackendUser($user);
        if ($backendUser === null) {
            $this->addFlashMessage(
                self::translate('backend_user_not_allowed_description', [$user]),
                self::translate('backend_user_not_allowed_title'),
                AbstractMessage::ERROR
            );

            // throws StopActionException - in v12 needs to return Response
            $this->redirect('index');
        }


        $this->addDetailButtons($backendUser);

        $this->view->assignMultiple(
            [
                'user' => $backendUser,
                'loginSessions' => $this->loginHistoryRepository->findUserLoginActions($backendUser),
            ]
        );
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    private function addDetailButtons(?BackendUser $backendUser): void
    {
        if ($backendUser === null) {
            return;
        }
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $editUserButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-open', Icon::SIZE_SMALL))
            ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit'))
            ->setHref(
                $this->backendUriBuilder->buildUriFromRoute('record_edit', [
                    'edit' => ['be_users' => [$backendUser->getUid() => 'edit']],
                    'returnUrl' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                ])
            );
        $buttonBar->addButton($editUserButton);

        if (!$backendUser->isCurrentlyLoggedIn()) {
            if ($backendUser->getIsDisabled()) {
                $enableUserButton = $buttonBar->makeLinkButton()
                    ->setIcon($this->iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL))
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:hide'))
                    ->setHref(
                        $this->backendUriBuilder->buildUriFromRoute('tce_db', [
                            'data' => ['be_users' => [$backendUser->getUid() => ['disable' => 0]]],
                            'redirect' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                        ])
                    );
                $buttonBar->addButton($enableUserButton);
            } else {
                $enableUserButton = $buttonBar->makeLinkButton()
                    ->setIcon($this->iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL))
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:unHide'))
                    ->setHref(
                        $this->backendUriBuilder->buildUriFromRoute('tce_db', [
                            'data' => ['be_users' => [$backendUser->getUid() => ['disable' => 1]]],
                            'redirect' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                        ])
                    );
                $buttonBar->addButton($enableUserButton);
            }
        }

        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementLoginhistory')
            ->setArguments(['tx_myusermanagement_myusermanagement_myusermanagementloginhistory' => ['action' => 'detail', 'user' => $backendUser->getUid()]])
            ->setDisplayName(LocalizationUtility::translate('myUserManagementLoginhistory', 'myUserManagement') . ': ' . $backendUser->getUsername());
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    private function initializeDefaultViewVariables(): void
    {
        $this->view->assignMultiple(
            [
                'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            ]
        );
    }
}
