<?php

defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function (string $extension): void {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'myusermanagement',
        '',
        '',
        null,
        [
            'name' => 'myusermanagement',
            'iconIdentifier' => 'module-my_user_management',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/Module.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MyUserManagement',
        'myusermanagement',
        'UserAdmin',
        '',
        [
            \KoninklijkeCollective\MyUserManagement\Controller\BackendUserController::class => 'index, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
            \KoninklijkeCollective\MyUserManagement\Controller\BackendUserGroupController::class => 'index',
        ],
        [
            'access' => 'user,group',
            'iconIdentifier' => 'module-my_user_management-user-admin',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAdmin.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MyUserManagement',
        'myusermanagement',
        'UserAccess',
        '',
        [\KoninklijkeCollective\MyUserManagement\Controller\UserAccessController::class => 'index'],
        [
            'access' => 'user,group',
            'iconIdentifier' => 'module-my_user_management-user-access',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAccess.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MyUserManagement',
        'myusermanagement',
        'FileMountAdmin',
        '',
        [\KoninklijkeCollective\MyUserManagement\Controller\FileMountController::class => 'index'],
        [
            'access' => 'user,group',
            'iconIdentifier' => 'module-my_user_management-file-mounts',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/FileMount.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'MyUserManagement',
        'myusermanagement',
        'LoginHistory',
        '',
        [\KoninklijkeCollective\MyUserManagement\Controller\LoginHistoryController::class => 'index, detail'],
        [
            'access' => 'user,group',
            'iconIdentifier' => 'module-my_user_management-login-history',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/LoginHistory.xlf',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][\KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission::KEY] =
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission::class
        );
    $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][\KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::KEY] =
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::class
        );
}, 'my_user_management');
