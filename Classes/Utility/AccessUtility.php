<?php
namespace KoninklijkeCollective\MyUserManagement\Utility;

class AccessUtility
{

    /**
     * Check if user has access to module
     *
     * @param string $moduleName
     * @return boolean
     */
    public static function beUserHasRightToSeeModule($moduleName = 'myusermanagement_module')
    {
        $hasAccess = false;
        if (\TYPO3\CMS\Backend\Utility\BackendUtility::isModuleSetInTBE_MODULES($moduleName)) {
            $hasAccess = static::getBackendUserAuthentication()->check('modules', $moduleName);
        }

        return $hasAccess;
    }

    /**
     * Check if user has access to table
     *
     * @param string $table
     * @return boolean
     */
    public static function beUserHasRightToEditTable($table = 'be_users')
    {
        return static::getBackendUserAuthentication()->check('tables_modify', $table);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}