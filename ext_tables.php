<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Adjust be_user TCA for editor access configuration
include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/BackendUser.php');

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
        array (),
        array (
            'access' => 'user, group',
            'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'userAdmin',
        '',
        array(
            'BackendUser' => 'list, online, compare, addToCompareList, removeFromCompareList, terminateBackendUserSession',
        ),
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_useradmin.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be_user_admin.xlf',
        )
    );


    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Serfhos.' . $_EXTKEY,
        'MyUserManagement',
        'userAccess',
        '',
        array(
            'UserAccess' => 'list',
        ),
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_useraccess.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be_user_access.xlf',
            'navigationComponentId' => 'typo3-pagetree',
        )
    );

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Serfhos.' . $_EXTKEY,
		'MyUserManagement',
		'groupAdmin',
		'',
		array(
			'BackendUserGroup' => 'list, compare, addToCompareList, removeFromCompareList',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/moduleicon_myusermanagement_groupadmin.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Backend/BackendUserGroup.xlf',
		)
	);

}
?>