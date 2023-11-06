<?php

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;

defined('TYPO3') or die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][BackendUserActionPermission::KEY] =
    new BackendUserActionPermission();
$GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][BackendUserGroupPermission::KEY] =
    new BackendUserGroupPermission();
