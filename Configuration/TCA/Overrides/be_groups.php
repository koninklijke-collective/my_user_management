<?php

use KoninklijkeCollective\MyUserManagement\Hook\TableConfigurationArrayHook;

defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function (string $table): void {
    // Enabling regular BE users to edit BE groups
    $GLOBALS['TCA'][$table]['ctrl']['adminOnly'] = 0;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreWebMountRestriction'] = 1;

    // Make sure user only shows configured groups
    // @obsolete since TYPO3 9.5
    // $GLOBALS['TCA'][$table]['columns']['subgroup']['config']['itemsProcFunc'] =
    //     TableConfigurationArrayHook::class . '->filterConfiguredBackendGroups';
    // For legacy override full subgroup config
    $GLOBALS['TCA'][$table]['columns']['subgroup']['config'] = [
        'type' => 'select',
        'renderType' => 'selectMultipleSideBySide',
        'enableMultiSelectFilterTextfield' => true,
        'itemsProcFunc' => TableConfigurationArrayHook::class . '->addGroupsForUser',
        'size' => 5,
        'autoSizeMax' => 50,
    ];

    // Make all fields to exclude for users
    foreach ($GLOBALS['TCA'][$table]['columns'] as $key => &$configuration) {
        if (!isset($configuration['exclude'])) {
            $configuration['exclude'] = 1;
        }
    }
}, 'be_groups');
