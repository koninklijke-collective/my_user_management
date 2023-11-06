<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Session\Backend\SessionBackendInterface;
use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class OnlineSessionService implements SingletonInterface
{
    protected ?array $userSessions = null;

    public function userIsCurrentlyLoggedIn(BackendUser $user): bool
    {
        return in_array($user->getUid(), $this->getSessions(), true);
    }

    public function getSessions(): array
    {
        if ($this->userSessions === null) {
            $sessions = $this->getSessionBackend()->getAll();
            $sessions = array_column($sessions, 'ses_userid');

            $sessions = array_unique($sessions);
            $this->userSessions = array_combine($sessions, $sessions);
        }

        return $this->userSessions;
    }

    protected function getSessionBackend(): SessionBackendInterface
    {
        return GeneralUtility::makeInstance(SessionManager::class)->getSessionBackend('BE');
    }
}
