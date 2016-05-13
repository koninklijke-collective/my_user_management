<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Enabling regular BE users to edit BE users
$GLOBALS['TCA']['be_users']['ctrl']['adminOnly'] = 0;
$GLOBALS['TCA']['be_users']['ctrl']['security']['ignoreRootLevelRestriction'] = 1;
$GLOBALS['TCA']['be_users']['ctrl']['security']['ignoreWebMountRestriction'] = 1;

// Make sure user only shows configured groups
$GLOBALS['TCA']['be_users']['columns']['usergroup']['config']['itemsProcFunc'] = 'KoninklijkeCollective\\MyUserManagement\\Hook\\TableConfigurationArrayHook->filterConfiguredBackendGroups';

// Make all fields to exclude for users
foreach ($GLOBALS['TCA']['be_users']['columns'] as $key => &$configuration) {
    $configuration['exclude'] = 1;

    switch ($key) {
        // Ignore certain fields
        case 'admin':
        case 'starttime':
        case 'endtime':
            $configuration['displayCond'] = 'HIDE_FOR_NON_ADMINS';

            break;
    }
}
