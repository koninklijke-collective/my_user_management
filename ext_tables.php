<?php

use KoninklijkeCollective\MyUserManagement\Controller\BackendUserController;
use KoninklijkeCollective\MyUserManagement\Controller\FileMountController;
use KoninklijkeCollective\MyUserManagement\Controller\LoginHistoryController;
use KoninklijkeCollective\MyUserManagement\Controller\UserAccessController;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3_MODE') or die('Access denied.');

ExtensionManagementUtility::addModule(
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

ExtensionUtility::registerModule(
    'MyUserManagement',
    'myusermanagement',
    'UserAdmin',
    '',
    [
        BackendUserController::class => 'index, show, addToCompareList, removeFromCompareList, removeAllFromCompareList, compare, online, terminateBackendUserSession, initiatePasswordReset, groups, addGroupToCompareList, removeGroupFromCompareList, removeAllGroupsFromCompareList, compareGroups',
    ],
    [
        'access' => 'user,group',
        'iconIdentifier' => 'module-my_user_management-user-admin',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAdmin.xlf',
    ]
);

ExtensionUtility::registerModule(
    'MyUserManagement',
    'myusermanagement',
    'UserAccess',
    '',
    [UserAccessController::class => 'index'],
    [
        'access' => 'user,group',
        'iconIdentifier' => 'module-my_user_management-user-access',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAccess.xlf',
        'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
    ]
);

ExtensionUtility::registerModule(
    'MyUserManagement',
    'myusermanagement',
    'FileMountAdmin',
    '',
    [FileMountController::class => 'index'],
    [
        'access' => 'user,group',
        'iconIdentifier' => 'module-my_user_management-file-mounts',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/FileMount.xlf',
    ]
);

ExtensionUtility::registerModule(
    'MyUserManagement',
    'myusermanagement',
    'LoginHistory',
    '',
    [LoginHistoryController::class => 'index, detail'],
    [
        'access' => 'user,group',
        'iconIdentifier' => 'module-my_user_management-login-history',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/LoginHistory.xlf',
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][BackendUserActionPermission::KEY] =
    GeneralUtility::makeInstance(BackendUserActionPermission::class);
$GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][BackendUserGroupPermission::KEY] =
    GeneralUtility::makeInstance(BackendUserGroupPermission::class);
