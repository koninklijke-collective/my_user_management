<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    // add module before 'Help'
    if (!isset($TBE_MODULES['MyUserManagementMyusermanagement'])) {
        $temp_TBE_MODULES = array();
        foreach ($TBE_MODULES as $key => $val) {
            if ($key == 'help') {
                $temp_TBE_MODULES['MyUserManagementMyusermanagement'] = '';
                $temp_TBE_MODULES[$key] = $val;
            } else {
                $temp_TBE_MODULES[$key] = $val;
            }
        }

        $TBE_MODULES = $temp_TBE_MODULES;
    }

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        '',
        '',
        array(),
        array(
            'access' => 'user, group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/Module.xml',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'UserAdmin',
        '',
        array(
            'BackendUser' => 'list, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_user.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/UserAdmin.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'UserAccess',
        '',
        array(
            'UserAccess' => 'list',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_useraccess.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/UserAccess.xlf',
            'navigationComponentId' => 'typo3-pagetree',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'GroupAdmin',
        '',
        array(
            'BackendUserGroup' => 'list, compare, addToCompareList, removeFromCompareList',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_group.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/BackendUserGroup.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'FileMountAdmin',
        '',
        array(
            'FileMount' => 'list, detail',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_filemount.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/FileMount.xlf',
        )
    );
}