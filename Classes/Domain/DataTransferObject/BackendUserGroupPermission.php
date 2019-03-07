<?php
namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * DTO: Permission access Backend User Groups
 *
 * @package KoninklijkeCollective\MyUserManagement\Domain\Model\DataTransferObject
 */
class BackendUserGroupPermission extends AbstractPermission
{

    /**
     * @var string
     */
    const KEY = 'my_user_management_group_permissions';



    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return void
     */
    protected function populateData()
    {
        $this->data = [
            'header' => 'LLL:EXT:my_user_management/Resources/Private/Language/locallang.xlf:backend_access_group_permissions',
            'items' => [],
        ];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('be_groups');

        $groups = $queryBuilder
            ->select('*')
            ->from('be_groups')
            ->execute()
            ->fetchAll();

        foreach ($groups as $group) {
            $this->data['items'][$group['uid']] = [
                $group['title'],
                'EXT:my_user_management/Resources/Public/Icons/table-user-group-backend.svg',
                $group['description'],
            ];
        }
    }

}
