<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($extKey, $mainModule) {
    // Avoid that this block is loaded in the frontend or within the upgrade-wizards
    if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

        // Register main module icon
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon('module-' . $mainModule, \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class, [
            'name' => 'user-secret'
        ]);

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
            $mainModule,
            '',
            '',
            null,
            [
                'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/Module.xlf',
                'iconIdentifier' => 'module-' . $mainModule
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'KoninklijkeCollective.' . $extKey,
            $mainModule,
            'UserAdmin',
            '',
            [
                'BackendUser' => 'index, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
                'BackendUserGroup' => 'index'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extKey . '/Resources/Public/Icons/module-user-admin.png',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend/UserAdmin.xlf',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'KoninklijkeCollective.' . $extKey,
            $mainModule,
            'UserAccess',
            '',
            [
                'UserAccess' => 'index',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extKey . '/Resources/Public/Icons/module-user-access.png',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend/UserAccess.xlf',
                'navigationComponentId' => 'typo3-pagetree',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'KoninklijkeCollective.' . $extKey,
            $mainModule,
            'FileMountAdmin',
            '',
            [
                'FileMount' => 'index',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extKey . '/Resources/Public/Icons/module-file-mounts.png',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend/FileMount.xlf',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'KoninklijkeCollective.' . $extKey,
            $mainModule,
            'LoginHistory',
            '',
            [
                'LoginHistory' => 'index, detail',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extKey . '/Resources/Public/Icons/module-login-history.png',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend/LoginHistory.xlf',
            ]
        );

        $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][\KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::KEY] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::class);
    }
}, $_EXTKEY, 'myusermanagement');
