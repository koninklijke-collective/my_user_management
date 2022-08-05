<?php

use KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook;
use KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook;

defined('TYPO3_MODE') or die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList']['my_user_management'] =
    DataHandlerCheckModifyAccessListHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook']['my_user_management'] =
    ButtonBarHook::class . '->getButtons';

