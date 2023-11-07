<?php

use KoninklijkeCollective\MyUserManagement\Hook\TableConfigurationArrayHook;

defined('TYPO3') or die('Access denied.');

call_user_func(function (string $table): void {
    // Enabling regular BE users to edit BE users
    $GLOBALS['TCA'][$table]['ctrl']['adminOnly'] = false;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreRootLevelRestriction'] = true;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreWebMountRestriction'] = true;

    // Make sure user only shows configured groups
    $GLOBALS['TCA'][$table]['columns']['usergroup']['config']['itemsProcFunc'] =
        TableConfigurationArrayHook::class . '->filterConfiguredBackendGroups';

    // Make sure admin is not configurable for non admins.
    $GLOBALS['TCA'][$table]['columns']['admin']['displayCond'] = 'HIDE_FOR_NON_ADMINS';

    // Make all fields to exclude for users
    foreach ($GLOBALS['TCA'][$table]['columns'] as $column => &$configuration) {
        // Avoid exclusion of default columns
        if (in_array($column, ['username', 'disable'], true)) {
            continue;
        }

        $configuration['exclude'] = true;
    }
}, 'be_users');
