<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;

/**
 * Repository: BackendUserGroup
 */
final class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{
    /**
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllConfigured()
    {
        if (!BackendUserGroupPermission::hasConfigured()) {
            return $this->findAll();
        }

        $query = $this->createQuery();
        $query->matching($query->logicalAnd([
            $query->in('uid', BackendUserGroupPermission::getConfigured())
        ]));

        return $query->execute();
    }
}
