<?php

defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function (string $table): void {
    // Enabling regular BE users to edit BE filemounts
    $GLOBALS['TCA'][$table]['ctrl']['adminOnly'] = 0;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
    $GLOBALS['TCA'][$table]['ctrl']['security']['ignoreWebMountRestriction'] = 1;

    // Make all fields to exclude for users
    foreach ($GLOBALS['TCA'][$table]['columns'] as $key => &$configuration) {
        if (!isset($configuration['exclude'])) {
            $configuration['exclude'] = 1;
        }
    }
}, 'sys_filemounts');
