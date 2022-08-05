<?php

use KoninklijkeCollective\MyUserManagement\Hook\TableConfigurationArrayHook;

defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function (string $table): void {
    // Enabling regular BE users to edit BE users
    $GLOBALS['TCA'][$table]['ctrl']['adminOnly'] = 0;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreWebMountRestriction'] = 1;

    // Make sure user only shows configured groups
    $GLOBALS['TCA'][$table]['columns']['usergroup']['config']['itemsProcFunc'] =
        TableConfigurationArrayHook::class . '->filterConfiguredBackendGroups';

    // Make sure admin is not configurable for non admins.
    $GLOBALS['TCA'][$table]['columns']['admin']['displayCond'] = 'HIDE_FOR_NON_ADMINS';

    // Make all fields to exclude for users
    foreach ($GLOBALS['TCA'][$table]['columns'] as &$configuration) {
        $configuration['exclude'] = 1;
    }
}, 'be_users');
