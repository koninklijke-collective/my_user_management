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
            'iconIdentifier' => 'module-myusermanagement',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/Module.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $extension,
        'myusermanagement',
        'UserAdmin',
        '',
        [
            'BackendUser' => 'index, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
            'BackendUserGroup' => 'index',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:my_user_management/Resources/Public/Icons/module-user-admin.png',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAdmin.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $extension,
        'myusermanagement',
        'UserAccess',
        '',
        ['UserAccess' => 'index'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:my_user_management/Resources/Public/Icons/module-user-access.png',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAccess.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $extension,
        'myusermanagement',
        'FileMountAdmin',
        '',
        ['FileMount' => 'index'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:my_user_management/Resources/Public/Icons/module-file-mounts.png',
            'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/FileMount.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $extension,
        'myusermanagement',
        'LoginHistory',
        '',
        ['LoginHistory' => 'index, detail'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:my_user_management/Resources/Public/Icons/module-login-history.png',
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
