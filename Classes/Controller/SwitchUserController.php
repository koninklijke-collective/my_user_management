<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace KoninklijkeCollective\MyUserManagement\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Authentication\Event\SwitchUserEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SysLog\Type;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Provide modified version of "switchUserAction" to allow non-admin users to switch users
 */
class SwitchUserController extends \TYPO3\CMS\Backend\Controller\SwitchUserController
{
    public function switchUserAction(ServerRequestInterface $request): ResponseInterface
    {
        $currentUser = $this->getBackendUserAuthentication();
        $targetUserId = (int)($request->getParsedBody()['targetUser'] ?? 0);

        // Removed condition part "|| !$currentUser->isAdmin()" to allow non-admin users to switch user
        if (!$targetUserId
            || $targetUserId === (int)($currentUser->user[$currentUser->userid_column] ?? 0)
            || $currentUser->getOriginalUserIdWhenInSwitchUserMode() !== null
        ) {
            return $this->jsonResponse(['success' => false]);
        }

        $targetUser = BackendUtility::getRecord('be_users', $targetUserId, '*', BackendUtility::BEenableFields('be_users'));
        if ($targetUser === null || $targetUser['admin'] === 1 || $targetUser['disable'] === 1) {
            return $this->jsonResponse(['success' => false]);
        }

        if (ExtensionManagementUtility::isLoaded('beuser')) {
            // Set backend user listing module as starting module if installed
            $currentUser->uc['startModuleOnFirstLogin'] = 'system_BeuserTxBeuser';
        }
        $currentUser->uc['recentSwitchedToUsers'] = $this->generateListOfMostRecentSwitchedUsers($targetUserId);
        $currentUser->writeUC();

        // Write user switch to log
        $currentUser->writelog(Type::LOGIN, 2, 0, 1, 'User %s switched to user %s (be_users:%s)', [
            $currentUser->user[$currentUser->username_column] ?? '',
            $targetUser['username'] ?? '',
            $targetUserId,
        ]);

        $sessionObject = $currentUser->getSession();
        $sessionObject->set('backuserid', (int)($currentUser->user[$currentUser->userid_column] ?? 0));
        $sessionRecord = $sessionObject->toArray();
        $sessionRecord['ses_userid'] = $targetUserId;
        $this->sessionBackend->update($sessionObject->getIdentifier(), $sessionRecord);
        // We must regenerate the internal session so the new ses_userid is present in the userObject
        $currentUser->enforceNewSessionId();

        $event = new SwitchUserEvent(
            $currentUser->getSession()->getIdentifier(),
            $targetUser,
            (array)$currentUser->user
        );
        $this->eventDispatcher->dispatch($event);

        return $this->jsonResponse([
            'success' => true,
            'url' => $this->uriBuilder->buildUriFromRoute('main')
        ]);
    }
}
