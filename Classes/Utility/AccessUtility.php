<?php
namespace KoninklijkeCollective\MyUserManagement\Utility;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Security\Exception;

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
    public static function beUserHasRightToSeeTable($table = 'be_users')
    {
        return static::getBackendUserAuthentication()->check('tables_select', $table);
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
     * Check if user can add table
     *
     * @param string $table
     * @return boolean
     * @throws Exception
     */
    public static function beUserHasRightToAddTable($table = 'be_users')
    {
        $allowed = false;
        if (static::beUserHasRightToEditTable($table)) {
            $allowed = true;

            // @todo, should be configurable
            switch ($table) {
                case BackendUser::TABLE:
                    $requiredFields = [
                        'username'
                    ];
                    break;
                case BackendUserGroup::TABLE:
                    $requiredFields = [
                        ''
                    ];
                    break;
                default:
                    throw new Exception('Unknown lookup for rights');
            }

            foreach ($requiredFields as $field) {
                if (static::getBackendUserAuthentication()->check('non_exclude_fields', $table . ':' . $field) === false) {
                    $allowed = false;
                    break;
                }
            }
        }
        return $allowed;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}