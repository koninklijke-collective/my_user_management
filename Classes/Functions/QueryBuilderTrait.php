<?php

namespace KoninklijkeCollective\MyUserManagement\Functions;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait QueryBuilderTrait
{
    /**
     * Get QueryBuilder without any default restrictions
     *
     * @param  string  $table
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected static function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        // Remove all restrictions
        $queryBuilder->getRestrictions()
            ->removeAll();

        return $queryBuilder;
    }
}
