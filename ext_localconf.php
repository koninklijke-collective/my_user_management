<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($extKey) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList'][] = \KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][] = \KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook::class . '->getButtons';
}, $_EXTKEY);
