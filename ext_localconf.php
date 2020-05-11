<?php

defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function ($extension): void {
    // Register main module icon
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'module-my_user_management', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'user-secret']
    );
    $iconRegistry->registerIcon(
        'module-my_user_management-overview', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/module-overview.svg']
    );
    $iconRegistry->registerIcon(
        'module-my_user_management-file-mounts', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/module-file-mounts.svg']
    );
    $iconRegistry->registerIcon(
        'module-my_user_management-login-history', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/module-login-history.svg']
    );
    $iconRegistry->registerIcon(
        'module-my_user_management-user-access', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/module-user-access.svg']
    );
    $iconRegistry->registerIcon(
        'module-my_user_management-user-admin', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/module-user-admin.svg']
    );
    $iconRegistry->registerIcon(
        'my_user_management-permissions-actions-add',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/permissions-actions-add.svg']
    );
    $iconRegistry->registerIcon(
        'my_user_management-permissions-actions-delete',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/permissions-actions-delete.svg']
    );
    $iconRegistry->registerIcon(
        'my_user_management-permissions-actions-user_switch',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:my_user_management/Resources/Public/Icons/permissions-actions-user-switch.svg']
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList'][$extension] =
        \KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][$extension] =
        \KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook::class . '->getButtons';
}, 'my_user_management');
