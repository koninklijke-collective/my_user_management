<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        '',
        '',
        array(),
        array(
            'access' => 'user, group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-overview.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/Module.xml',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
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
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
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
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
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
}