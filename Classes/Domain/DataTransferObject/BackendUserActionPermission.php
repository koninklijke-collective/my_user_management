<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

/**
 * DTO: Permission access Backend User Actions
 */
final class BackendUserActionPermission extends AbstractPermission
{
    use PermissionTrait;

    public const KEY = 'my_user_management_action_permissions';

    /**
     * Disabled actions
     */
    public const ACTION_ADD_USER = 1;
    public const ACTION_DELETE_USER = 2;
    public const ACTION_ADD_GROUP = 3;
    public const ACTION_DELETE_GROUP = 4;
    public const ACTION_SWITCH_USER = 5;
    public const ACTION_ADD_FILEMOUNT = 6;
    public const ACTION_DELETE_FILEMOUNT = 7;

    public static function getItems(): array
    {
        return [
            self::ACTION_ADD_USER => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_add_user.title',
                'my_user_management-permissions-actions-add',
            ],
            self::ACTION_DELETE_USER => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_delete_user.title',
                'my_user_management-permissions-actions-delete',
            ],
            self::ACTION_ADD_GROUP => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_add_group.title',
                'my_user_management-permissions-actions-add',
            ],
            self::ACTION_DELETE_GROUP => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_delete_group.title',
                'my_user_management-permissions-actions-delete',
            ],
            self::ACTION_SWITCH_USER => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_switch_user.title',
                'my_user_management-permissions-actions-user_switch',
            ],
            self::ACTION_ADD_FILEMOUNT => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_add_filemount.title',
                'my_user_management-permissions-actions-add',
            ],
            self::ACTION_DELETE_FILEMOUNT => [
                'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions.action_delete_filemount.title',
                'my_user_management-permissions-actions-delete',
            ],
        ];
    }

    protected function populateData(): void
    {
        $this->data = [
            'header' => 'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_action_permissions',
            'items' => self::getItems(),
        ];
    }

    /** Helper classes for readability */

    public static function userCreationAllowed(): bool
    {
        return self::isConfigured(self::ACTION_ADD_USER);
    }

    public static function userDeletionAllowed(): bool
    {
        return self::isConfigured(self::ACTION_DELETE_USER);
    }

    public static function groupCreationAllowed(): bool
    {
        return self::isConfigured(self::ACTION_ADD_GROUP);
    }

    public static function groupDeletionAllowed(): bool
    {
        return self::isConfigured(self::ACTION_DELETE_GROUP);
    }

    public static function switchUserAllowed(): bool
    {
        return self::isConfigured(self::ACTION_SWITCH_USER);
    }

    public static function filemountCreationAllowed(): bool
    {
        return self::isConfigured(self::ACTION_ADD_FILEMOUNT);
    }

    public static function filemountDeletionAllowed(): bool
    {
        return self::isConfigured(self::ACTION_DELETE_FILEMOUNT);
    }
}
