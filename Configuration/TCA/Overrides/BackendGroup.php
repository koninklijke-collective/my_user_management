<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// enabling regular BE users to edit BE groups
$GLOBALS['TCA']['be_groups']['ctrl']['adminOnly'] = 0;
$GLOBALS['TCA']['be_groups']['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
$GLOBALS['TCA']['be_groups']['ctrl']['security']['ignoreWebMountRestriction'] = 1;

// make all fields to exclude for users
foreach ($GLOBALS['TCA']['be_groups']['columns'] as $key => &$configuration) {
    if (!isset($configuration['exclude'])) {
        $configuration['exclude'] = 1;
    }
}

$GLOBALS['TCA']['be_groups']['columns']['subgroup']['config']['itemsProcFunc'] = 'Serfhos\\MyUserManagement\\Hooks\\ItemsProcFunc->getRestrictedBackendUserGroups';
