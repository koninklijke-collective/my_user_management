<?php

use KoninklijkeCollective\MyUserManagement\Hook\TableConfigurationArrayHook;

defined('TYPO3') or die('Access denied.');

call_user_func(function (string $table): void {
    // Enabling regular BE users to edit BE groups
    $GLOBALS['TCA'][$table]['ctrl']['adminOnly'] = false;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreRootLevelRestriction'] = true;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreWebMountRestriction'] = true;

    // Make sure user only shows configured groups
    $GLOBALS['TCA'][$table]['columns']['subgroup']['config']['itemsProcFunc'] =
        TableConfigurationArrayHook::class . '->filterConfiguredBackendGroups';

    // Make all fields to exclude for users
    foreach ($GLOBALS['TCA'][$table]['columns'] as $column => &$configuration) {
        // Avoid exclusion of default columns
        if (in_array($column, ['title', 'hidden'], true)) {
            continue;
        }

        if (!isset($configuration['exclude'])) {
            $configuration['exclude'] = true;
        }
    }
}, 'be_groups');
