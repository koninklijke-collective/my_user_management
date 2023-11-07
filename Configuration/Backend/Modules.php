<?php

use KoninklijkeCollective\MyUserManagement\Controller\BackendUserController;
use KoninklijkeCollective\MyUserManagement\Controller\LoginHistoryController;
use KoninklijkeCollective\MyUserManagement\Controller\UserAccessController;

return [
    'myusermanagement' => [
        'parent' => '',
        'position' => '',
        'access' => '',
        'path' => null,
        'iconIdentifier' => 'module-my_user_management',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/Module.xlf',
        'iconIdentifier',
    ],
    'myusermanagement_user_admin' => [
        'parent' => 'myusermanagement',
        'access' => 'user',
        'path' => '/module/my-user-management/user-admin',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAdmin.xlf',
        'extensionName' => 'MyUserManagement',
        'iconIdentifier' => 'module-my_user_management-user-admin',
        'controllerActions' => [
            BackendUserController::class => [
                'index',
                'show',
                'addToCompareList',
                'removeFromCompareList',
                'removeAllFromCompareList',
                'compare',
                'online',
                'terminateBackendUserSession',
                'initiatePasswordReset',
                'groups',
                'addGroupToCompareList',
                'removeGroupFromCompareList',
                'removeAllGroupsFromCompareList',
                'compareGroups',
                'filemounts',
            ],
        ],
    ],
    'myusermanagement_user_access' => [
        'parent' => 'myusermanagement',
        'access' => 'user',
        'path' => '/module/my-user-management/user-access',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/UserAccess.xlf',
        'extensionName' => 'MyUserManagement',
        'iconIdentifier' => 'module-my_user_management-user-access',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'controllerActions' => [
            UserAccessController::class => ['index'],
        ],
    ],
    'myusermanagement_login_history' => [
        'parent' => 'myusermanagement',
        'access' => 'user',
        'path' => '/module/my-user-management/login-history',
        'labels' => 'LLL:EXT:my_user_management/Resources/Private/Language/Backend/LoginHistory.xlf',
        'extensionName' => 'MyUserManagement',
        'iconIdentifier' => 'module-my_user_management-login-history',
        'controllerActions' => [
            LoginHistoryController::class => ['index', 'detail'],
        ],
    ],
];
