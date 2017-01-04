<?php
namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

/**
 * DTO: Permission access Backend User Actions
 *
 * @package KoninklijkeCollective\MyUserManagement\Domain\Model\DataTransferObject
 */
class BackendUserActionPermission extends AbstractPermission
{

    /**
     * @var string
     */
    const KEY = 'my_user_management_action_permissions';

    /**
     * Disabled actions
     */
    const ACTION_ADD_USER = 1;
    const ACTION_DELETE_USER = 2;
    const ACTION_ADD_GROUP = 3;
    const ACTION_DELETE_GROUP = 4;
    const ACTION_SWITCH_USER = 5;

    /**
     * @return void
     */
    protected function populateData()
    {
        $this->data = [
            'header' => 'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions',
            'items' => [
                static::ACTION_ADD_USER => [
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_add_user.title',
                    'EXT:my_user_management/Resources/Public/Icons/permissions-actions-add.svg',
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_add_user.description',
                ],
                static::ACTION_DELETE_USER => [
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_delete_user.title',
                    'EXT:my_user_management/Resources/Public/Icons/permissions-actions-delete.svg',
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_delete_user.description',
                ],
                static::ACTION_ADD_GROUP => [
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_add_group.title',
                    'EXT:my_user_management/Resources/Public/Icons/permissions-actions-add.svg',
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_add_group.description',
                ],
                static::ACTION_DELETE_GROUP => [
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_delete_group.title',
                    'EXT:my_user_management/Resources/Public/Icons/permissions-actions-delete.svg',
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_delete_group.description',
                ],
                static::ACTION_SWITCH_USER => [
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_switch_user.title',
                    'EXT:my_user_management/Resources/Public/Icons/permissions-actions-user-switch.svg',
                    'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_action_permissions.action_switch_user.description',
                ],
            ],
        ];
    }

    /**
     * Quickly check if user has access
     *
     * @param integer $action
     * @return boolean
     */
    public static function isConfigured($action)
    {
        return in_array($action, static::configured());
    }

}
