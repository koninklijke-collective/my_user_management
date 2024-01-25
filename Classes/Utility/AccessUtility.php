<?php

namespace KoninklijkeCollective\MyUserManagement\Utility;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Utility: Access Permissions for Backend User Authentication
 */
final class AccessUtility
{
    use BackendUserAuthenticationTrait;

    /**
     * Check if user has access to module
     *
     * @param  string  $moduleName
     * @return bool
     */
    public static function beUserHasRightToSeeModule(string $moduleName = 'myusermanagement_module', BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        $hasAccess = false;
        if (BackendUtility::isModuleSetInTBE_MODULES($moduleName)) {
            $hasAccess = $backendUserAuthentication->check('modules', $moduleName);
        }

        return $hasAccess;
    }

    /**
     * Check if user has access to table
     *
     * @param  string  $table
     * @return bool
     */
    public static function beUserHasRightToSeeTable(string $table, BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        return $backendUserAuthentication->check('tables_select', $table);
    }

    /**
     * Check if user has access to table
     *
     * @param  string  $table
     * @return bool
     */
    public static function beUserHasRightToEditTable(string $table, BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        if (!$backendUserAuthentication->check('tables_modify', $table)) {
            return false;
        }

        // Check minimal required field for tables
        switch ($table) {
            case BackendUser::TABLE:
                return self::beUserHasRightToEditTableField($table, 'username', $backendUserAuthentication);

            case BackendUserGroup::TABLE:
                return self::beUserHasRightToEditTableField($table, 'title', $backendUserAuthentication);
        }

        return true;
    }

    /**
     * Check if user has access to table field
     *
     * @param  string  $table
     * @param  string  $field
     * @return bool
     */
    public static function beUserHasRightToEditTableField(string $table, string $field, BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        return $backendUserAuthentication->check('non_exclude_fields', $table . ':' . $field);
    }

    /**
     * Check if user can add table
     *
     * @param  string  $table
     * @return bool
     */
    public static function beUserHasRightToAddTable(string $table, BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        if (self::beUserHasRightToEditTable($table, $backendUserAuthentication)) {
            switch ($table) {
                case BackendUser::TABLE:
                    if (!BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_USER)) {
                        return false;
                    }

                    return true;
                case BackendUserGroup::TABLE:
                    if (!BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_GROUP)) {
                        return false;
                    }

                    return true;
                case FileMount::TABLE:
                    if (!BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_FILEMOUNT)) {
                        return false;
                    }

                    return true;
            }
        }

        return false;
    }

    /**
     * Check if user can add table
     *
     * @param  string  $table
     * @return bool
     */
    public static function beUserHasRightToDeleteTable(string $table, BackendUserAuthentication $backendUserAuthentication = null): bool
    {
        $backendUserAuthentication = $backendUserAuthentication ?? self::getBackendUserAuthentication();

        if ($backendUserAuthentication->isAdmin()) {
            return true;
        }

        switch ($table) {
            case BackendUser::TABLE:
                return BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_USER);
            case BackendUserGroup::TABLE:
                return BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_GROUP);
            case FileMount::TABLE:
                return BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_FILEMOUNT);
        }

        return false;
    }
}
