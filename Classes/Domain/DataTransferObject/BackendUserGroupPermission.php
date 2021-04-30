<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DTO: Permission access Backend User Groups
 */
final class BackendUserGroupPermission extends AbstractPermission
{
    use PermissionTrait;

    /**
     * @var string
     */
    public const KEY = 'my_user_management_group_permissions';

    /**
     * @return void
     */
    protected function populateData(): void
    {
        $this->data = [
            'header' => 'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_group_permissions',
            'items' => [],
        ];
        foreach ($this->getBackendGroupsForList() as $group) {
            $this->data['items'][$group['uid']] = [
                $group['title'],
                'EXT:my_user_management/Resources/Public/Icons/table-user-group-backend.svg',
                $group['description'],
            ];
        }
    }

    /**
     * @return array
     */
    protected function getBackendGroupsForList(): array
    {
        $queryBuilder = $this->getQueryBuilderForTable('be_groups');
        $query = $queryBuilder->select('uid', 'title', 'description')
            ->from('be_groups');

        return $query->execute()->fetchAll();
    }

    /**
     * Get QueryBuilder without any default restrictions
     *
     * @param  string  $table
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        // Show all records except versioning placeholders
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class))
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder;
    }

    /**
     * @param  int  $group
     * @return bool
     */
    public static function hasAccessToGroup(int $group): bool
    {
        return self::isConfigured($group);
    }
}
