<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Controller: User Login History
 */
final class LoginHistoryController extends ActionController
{
    use TranslateTrait;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository */
    protected $loginHistoryRepository;

    /** @var \KoninklijkeCollective\MyUserManagement\Service\BackendUserService */
    protected $backendUserService;

    /** @var \KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService */
    protected $onlineSessionService;

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository  $loginHistoryRepository
     * @param  \KoninklijkeCollective\MyUserManagement\Service\BackendUserService  $backendUserService
     * @param  \KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService  $onlineSessionService
     */
    public function __construct(
        LoginHistoryRepository $loginHistoryRepository,
        BackendUserService $backendUserService,
        OnlineSessionService $onlineSessionService
    ) {
        $this->loginHistoryRepository = $loginHistoryRepository;
        $this->backendUserService = $backendUserService;
        $this->onlineSessionService = $onlineSessionService;
    }

    /**
     * @param  \TYPO3\CMS\Extbase\Mvc\View\ViewInterface  $view
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        $view->assignMultiple([
            'shortcutLabel' => 'MyLoginHistory',
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    /**
     * @return void
     * @throws \Exception when \DateTime cannot be created through settings
     */
    public function indexAction(): void
    {
        $loginSince = new DateTime($this->settings['since'] ?? '- 6 months');

        $this->view->assignMultiple([
            'onlineBackendUsers' => $this->onlineSessionService->getSessions(),
            'backendUsers' => $this->backendUserService->findAllBackendUsersForDropdown(),
            'loginHistory' => $this->loginHistoryRepository->lastLoggedInUsers(),
            'inactiveUsers' => $this->backendUserService->findAllInactiveBackendUsers($loginSince),
        ]);
    }

    /**
     * Action: Get login overview
     *
     * @param  int |null  $user
     * @return void
     */
    public function detailAction(?int $user = null): void
    {
        // Forward POST request to GET for correct workflow
        if ($this->request instanceof Request && $this->request->getMethod() === 'POST') {
            $this->redirect('detail', null, null, ['user' => $user]);
        }

        if ($user === null) {
            $this->redirect('index');

            return;
        }

        $backendUser = $this->backendUserService->findBackendUser($user);
        if ($backendUser === null) {
            $this->addFlashMessage(
                self::translate('backend_user_not_allowed_description', [$user]),
                self::translate('backend_user_not_allowed_title'),
                AbstractMessage::ERROR
            );

            return;
        }

        $this->view->assignMultiple([
            'user' => $backendUser,
            'loginSessions' => $this->loginHistoryRepository->findUserLoginActions($backendUser),
        ]);
    }
}
