<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Enabling regular BE users to edit BE filemounts
$GLOBALS['TCA']['sys_filemounts']['ctrl']['adminOnly'] = 0;
$GLOBALS['TCA']['sys_filemounts']['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
$GLOBALS['TCA']['sys_filemounts']['ctrl']['security']['ignoreWebMountRestriction'] = 1;

// Make all fields to exclude for users
foreach ($GLOBALS['TCA']['sys_filemounts']['columns'] as $key => &$configuration) {
    if (!isset($configuration['exclude'])) {
        $configuration['exclude'] = 1;
    }
}
