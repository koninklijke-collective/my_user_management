<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\QueryBuilderTrait;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

final class LoginHistoryRepository
{
    use QueryBuilderTrait;
    use BackendUserAuthenticationTrait;

    /**
     * Default static values for readability
     */
    public const TYPE_LOGGED_IN = 255;
    public const ACTION_LOG_IN = 1;

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser  $user
     * @return array
     */
    public function findUserLoginActions(BackendUser $user): array
    {
        $queryBuilder = static::getQueryBuilderForTable('sys_log');

        $query = $this->getQueryForUserLoginHistory($queryBuilder);
        $query->andWhere($queryBuilder->expr()->eq('be_users.uid', $user->getUid()));

        return $query->execute()->fetchAll();
    }

    /**
     * @param  int  $max
     * @return array
     */
    public function lastLoggedInUsers(int $max = 20): array
    {
        $queryBuilder = static::getQueryBuilderForTable('sys_log');

        $query = $this->getQueryForUserLoginHistory($queryBuilder);
        $query->setMaxResults($max);

        return $query->execute()->fetchAll();
    }

    /**
     * Create generic query for finding login history from sys_log
     *
     * @param  \TYPO3\CMS\Core\Database\Query\QueryBuilder  $queryBuilder
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    public function getQueryForUserLoginHistory(QueryBuilder $queryBuilder): QueryBuilder
    {
        $query = $queryBuilder
            ->select('sys_log.tstamp as login_date', 'sys_log.IP as login_ip', 'be_users.*')
            ->from('sys_log')
            ->join(
                'sys_log',
                'be_users',
                'be_users',
                $queryBuilder->expr()->eq('be_users.uid', $queryBuilder->quoteIdentifier('sys_log.userId'))
            )
            // Avoid deleted/disabled users
            ->where(
                $queryBuilder->expr()->eq('be_users.deleted', 0),
                $queryBuilder->expr()->eq('be_users.disable', 0)
            )

            // Make sure to query login logs
            ->andWhere($queryBuilder->expr()->gt('sys_log.userId', 0))
            ->andWhere($queryBuilder->expr()->gt('sys_log.tstamp', 0))
            ->andWhere($queryBuilder->expr()->eq('sys_log.level', 0))
            ->andWhere($queryBuilder->expr()->eq('sys_log.type', self::TYPE_LOGGED_IN))
            ->andWhere($queryBuilder->expr()->eq('sys_log.action', self::ACTION_LOG_IN))

            // Order by tstamp
            ->orderBy('sys_log.tstamp', 'desc')

            // Make sure only one login is shown per user
            ->groupBy('be_users.uid');

        if (!static::getBackendUserAuthentication()->isAdmin()) {
            $query->andWhere($queryBuilder->expr()->eq('be_users.admin', 0));
        }

        return $query;
    }
}
