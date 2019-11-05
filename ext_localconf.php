<?php
defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function ($extension): void {
    // Register main module icon
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'module-myusermanagement', // Hardcoded module icon name from core
        \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
        ['name' => 'user-secret']
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

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList'][] = \KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][] = \KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook::class . '->getButtons';
}, $_EXTKEY);
