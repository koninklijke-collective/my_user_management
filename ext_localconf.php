<?php

defined('TYPO3_MODE') or die('Access denied.');

(static function (string $extension): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList'][$extension] =
        \KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][$extension] =
        \KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook::class . '->getButtons';
})('my_user_management');
