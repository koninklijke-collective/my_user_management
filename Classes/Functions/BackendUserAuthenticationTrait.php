<?php

namespace KoninklijkeCollective\MyUserManagement\Functions;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

trait BackendUserAuthenticationTrait
{
    protected static function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
