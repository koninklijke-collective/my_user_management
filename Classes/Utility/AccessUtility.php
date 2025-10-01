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

        return self::getBackendUserAuthentication()->check('tables_modify', $table);
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

        if (!self::beUserHasRightToEditTable($table)) {
            return false;
        }

        return match ($table) {
            BackendUser::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_USER),
            BackendUserGroup::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_GROUP),
            // Bugged so cant adjust or edit
            // FileMount::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_ADD_FILEMOUNT),
            default => false,
        };
    }

    public static function beUserHasRightToDeleteTable(string $table): bool
    {
        if (!self::beUserHasRightToEditTable($table)) {
            return false;
        }

        return match ($table) {
            BackendUser::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_USER),
            BackendUserGroup::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_GROUP),

            // Bugged so cant adjust or edit
            // FileMount::TABLE => BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_FILEMOUNT),
            default => false,
        };
    }
}
