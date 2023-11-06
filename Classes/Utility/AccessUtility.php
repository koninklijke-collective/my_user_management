<?php

namespace KoninklijkeCollective\MyUserManagement\Utility;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Backend\Module\ModuleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility: Access Permissions for Backend User Authentication
 */
final class AccessUtility
{
    use BackendUserAuthenticationTrait;

    public static function beUserHasRightToSeeModule(string $moduleName = 'myusermanagement_module'): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        $hasAccess = false;
        if (GeneralUtility::makeInstance(ModuleProvider::class)->isModuleRegistered($moduleName)) {
            $hasAccess = self::getBackendUserAuthentication()->check('modules', $moduleName);
        }

        return $hasAccess;
    }

    public static function beUserHasRightToSeeTable(string $table): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        return self::getBackendUserAuthentication()->check('tables_select', $table);
    }

    public static function beUserHasRightToEditTable(string $table): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        if (!self::getBackendUserAuthentication()->check('tables_modify', $table)) {
            return false;
        }

        // Check minimal required field for tables
        return match ($table) {
            BackendUser::TABLE => self::beUserHasRightToEditTableField($table, 'username'),
            BackendUserGroup::TABLE => self::beUserHasRightToEditTableField($table, 'title'),
            default => true,
        };
    }

    public static function beUserHasRightToEditTableField(string $table, string $field): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        return self::getBackendUserAuthentication()->check('non_exclude_fields', $table . ':' . $field);
    }

    public static function beUserHasRightToAddTable(string $table): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        if (self::beUserHasRightToEditTable($table)) {
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

    public static function beUserHasRightToDeleteTable(string $table): bool
    {
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return true;
        }

        return match ($table) {
            BackendUser::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_USER),
            BackendUserGroup::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_GROUP),
            FileMount::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_FILEMOUNT),
            default => false,
        };
    }
}
