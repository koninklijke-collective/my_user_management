<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    $mainModule = 'myusermanagement';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        $mainModule,
        '',
        '',
        'EXT:my_user_management/Resources/Private/Modules/Container/'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $_EXTKEY,
        $mainModule,
        'UserAdmin',
        '',
        array(
            'BackendUser' => 'index, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
            'BackendUserGroup' => 'index'
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-user-admin.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/UserAdmin.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $_EXTKEY,
        $mainModule,
        'UserAccess',
        '',
        array(
            'UserAccess' => 'index',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-user-access.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/UserAccess.xlf',
            'navigationComponentId' => 'typo3-pagetree',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $_EXTKEY,
        $mainModule,
        'FileMountAdmin',
        '',
        array(
            'FileMount' => 'index',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-file-mounts.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/FileMount.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'KoninklijkeCollective.' . $_EXTKEY,
        $mainModule,
        'LoginHistory',
        '',
        array(
            'LoginHistory' => 'index, detail',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-login-history.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/LoginHistory.xlf',
        )
    );

    $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][\KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::KEY] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('KoninklijkeCollective\\MyUserManagement\\Domain\\DataTransferObject\\BackendUserGroupPermission');
}
