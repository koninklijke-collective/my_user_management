<?php

namespace KoninklijkeCollective\MyUserManagement\Utility;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Security\Exception;

class AccessUtility
{

    /**
     * Check if user has access to module
     *
     * @param  string  $moduleName
     * @return boolean
     */
    public static function beUserHasRightToSeeModule($moduleName = 'myusermanagement_module')
    {
        $hasAccess = false;
        if (BackendUtility::isModuleSetInTBE_MODULES($moduleName)) {
            $hasAccess = static::getBackendUserAuthentication()->check('modules', $moduleName);
        }

        return $hasAccess;
    }

    /**
     * Check if user has access to table
     *
     * @param  string  $table
     * @return boolean
     */
    public static function beUserHasRightToSeeTable($table = 'be_users')
    {
        return static::getBackendUserAuthentication()->check('tables_select', $table);
    }

    /**
     * Check if user has access to table
     *
     * @param  string  $table
     * @return boolean
     */
    public static function beUserHasRightToEditTable($table = 'be_users')
    {
        return static::getBackendUserAuthentication()->check('tables_modify', $table);
    }

    /**
     * Check if user has access to table field
     *
     * @param  string  $table
     * @param  string  $field
     * @return bool
     */
    public static function beUserHasRightToEditTableField($table = 'be_users', $field = '')
    {
        return static::getBackendUserAuthentication()->check('non_exclude_fields', $table . ':' . $field);
    }

    /**
     * Check if user can add table
     *
     * @param  string  $table
     * @return boolean
     * @throws \TYPO3\CMS\Extbase\Security\Exception
     */
    public static function beUserHasRightToAddTable($table = 'be_users')
    {
        if (static::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        $allowed = false;
        if (static::beUserHasRightToEditTable($table)) {
            $allowed = true;
            // @todo, should be configurable
            switch ($table) {
                case BackendUser::TABLE:
                    if (BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_USER) === false) {
                        return false;
                    }

                    $requiredFields = [
                        'username',
                    ];
                    break;
                case BackendUserGroup::TABLE:
                    if (BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_GROUP) === false) {
                        return false;
                    }

                    $requiredFields = [
                        'title',
                    ];
                    break;
                default:
                    throw new Exception('Unknown lookup for rights');
            }

            foreach ($requiredFields as $field) {
                if (static::getBackendUserAuthentication()
                        ->check('non_exclude_fields', $table . ':' . $field) === false) {
                    $allowed = false;
                    break;
                }
            }
        }

        return $allowed;
    }

    /**
     * Check if user can add table
     *
     * @param  string  $table
     * @return boolean
     */
    public static function beUserHasRightToDeleteTable($table = 'be_users')
    {
        if (static::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        switch ($table) {
            case BackendUser::TABLE:
                return BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_USER);
            case BackendUserGroup::TABLE:
                return BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_GROUP);
        }

        return false;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
