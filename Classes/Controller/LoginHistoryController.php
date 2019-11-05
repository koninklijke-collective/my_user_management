<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;

/**
 * Controller: User Login History
 */
final class LoginHistoryController extends ActionController
{
    use TranslateTrait;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $loginHistoryRepository;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Service\BackendUserService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $backendUserService;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $onlineSessionService;

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
        $loginSince = new \DateTime($this->settings['since'] ?? '- 6 months');

        $this->view->assignMultiple([
            'onlineBackendUsers' => $this->getOnlineSessionService()->getSessions(),
            'backendUsers' => $this->getBackendUserService()->findAllBackendUsersForDropdown(),
            'loginHistory' => $this->getLoginHistoryRepository()->lastLoggedInUsers(),
            'inactiveUsers' => $this->getBackendUserService()->findAllInactiveBackendUsers($loginSince),
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

        $backendUser = $this->getBackendUserService()->findBackendUser($user);
        if ($backendUser === null) {
            $this->addFlashMessage(
                static::translate('backend_user_not_allowed_description', [$user]),
                static::translate('backend_user_not_allowed_title'),
                AbstractMessage::ERROR
            );

            return;
        }

        $this->view->assignMultiple([
            'user' => $backendUser,
            'loginSessions' => $this->getLoginHistoryRepository()->findUserLoginActions($backendUser),
        ]);
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Repository\LoginHistoryRepository
     */
    protected function getLoginHistoryRepository(): LoginHistoryRepository
    {
        if ($this->loginHistoryRepository === null) {
            $this->loginHistoryRepository = $this->objectManager->get(LoginHistoryRepository::class);
        }

        return $this->loginHistoryRepository;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\BackendUserService
     */
    protected function getBackendUserService(): BackendUserService
    {
        if ($this->backendUserService === null) {
            $this->backendUserService = $this->objectManager->get(BackendUserService::class);
        }

        return $this->backendUserService;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService
     */
    protected function getOnlineSessionService(): OnlineSessionService
    {
        if ($this->onlineSessionService === null) {
            $this->onlineSessionService = $this->objectManager->get(OnlineSessionService::class);
        }

        return $this->onlineSessionService;
    }
}
