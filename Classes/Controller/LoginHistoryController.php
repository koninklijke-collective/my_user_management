<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[Controller]
final class LoginHistoryController extends ActionController
{
    use TranslateTrait;

    private \TYPO3\CMS\Backend\Template\ModuleTemplate $moduleTemplate;

    public function __construct(
        private readonly BackendUserService $backendUserService,
        private readonly LoginHistoryRepository $loginHistoryRepository,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly OnlineSessionService $onlineSessionService,
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
        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:my_user_management/Resources/Private/Language/Backend/FileMount.xlf:mlang_tabs_tab'));
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());

        $this->moduleTemplate->assignMultiple([
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    public function indexAction(): ResponseInterface
    {
        // Add shortcut possibility
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_login_history')
            ->setDisplayName(LocalizationUtility::translate('myUserManagementLoginhistory', 'myUserManagement'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        // Render variables
        $loginSince = new DateTime($this->settings['since'] ?? '- 6 months');
        $this->moduleTemplate->assignMultiple([
            'onlineBackendUsers' => $this->onlineSessionService->getSessions(),
            'backendUsers' => $this->backendUserService->findAllBackendUsersForDropdown(),
            'loginHistory' => $this->loginHistoryRepository->lastLoggedInUsers(),
            'inactiveUsers' => $this->backendUserService->findAllInactiveBackendUsers($loginSince),
        ]);

        return $this->moduleTemplate->renderResponse('LoginHistory/List');
    }

    public function detailAction(?int $user = null): ResponseInterface
    {  // Forward POST request to GET for correct workflow
        if ($this->request->getMethod() === 'POST') {
            return $this->redirect('detail', null, null, ['user' => $user]);
        }
        if ($user === null) {
            return $this->redirect('index');
        }
        $backendUser = $this->backendUserService->findBackendUser($user);
        if ($backendUser === null) {
            $this->addFlashMessage(
                self::translate('backend_user_not_allowed_description', [$user]),
                self::translate('backend_user_not_allowed_title'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );

            return $this->redirect('index');
        }

        // Register available buttons
        $this->registerDetailButtons($backendUser);

        // Render variables
        $this->moduleTemplate->assignMultiple([
            'user' => $backendUser,
            'loginSessions' => $this->loginHistoryRepository->findUserLoginActions($backendUser),
        ]);

        return $this->moduleTemplate->renderResponse('LoginHistory/Detail');
    }

    private function registerDetailButtons(?BackendUser $backendUser): void
    {
        if ($backendUser === null) {
            return;
        }
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $editUserButton = $buttonBar->makeLinkButton()
            ->setIcon($iconFactory->getIcon('actions-open', Icon::SIZE_SMALL))
            ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit'))
            ->setHref($uriBuilder->buildUriFromRoute('record_edit', [
                'edit' => ['be_users' => [$backendUser->getUid() => 'edit']],
                'returnUrl' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
            ]));
        $buttonBar->addButton($editUserButton);

        if (!$backendUser->isCurrentlyLoggedIn()) {
            if ($backendUser->getIsDisabled()) {
                $enableUserButton = $buttonBar->makeLinkButton()
                    ->setIcon($iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL))
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:hide'))
                    ->setHref($uriBuilder->buildUriFromRoute('tce_db', [
                        'data' => ['be_users' => [$backendUser->getUid() => ['disable' => 0]]],
                        'redirect' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                    ]));
                $buttonBar->addButton($enableUserButton);
            } else {
                $enableUserButton = $buttonBar->makeLinkButton()
                    ->setIcon($iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL))
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:unHide'))->setHref(
                        $uriBuilder->buildUriFromRoute('tce_db', [
                            'data' => ['be_users' => [$backendUser->getUid() => ['disable' => 1]]],
                            'redirect' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                        ]));
                $buttonBar->addButton($enableUserButton);
            }
        }

        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_login_history')
            ->setArguments([
                'action' => 'detail',
                'user' => $backendUser->getUid(),
            ])
            ->setDisplayName(LocalizationUtility::translate('myUserManagementLoginhistory', 'myUserManagement') . ': ' . $backendUser->getUsername());
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }
}
