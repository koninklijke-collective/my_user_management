<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DTO: Permission access Backend User Groups
 */
final class BackendUserGroupPermission extends AbstractPermission
{
    use PermissionTrait;

    public const KEY = 'my_user_management_group_permissions';

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

    private function getBackendGroupsForList(): array
    {
        $query = $this->getQueryBuilderForTable('be_groups')->select('uid', 'title', 'description')
            ->from('be_groups');

        return $query->executeQuery()->fetchAllAssociative();
    }

    /**
     * Get QueryBuilder without any default restrictions
     */
    private function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        // Show all records except versioning placeholders
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(WorkspaceRestriction::class))
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder;
    }

    public static function hasAccessToGroup(int $group): bool
    {
        return self::isConfigured($group);
    }
}
