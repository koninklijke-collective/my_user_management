<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;

/**
 * Repository: BackendUserGroup
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{

    /**
     * Returns all allowed objects of this repository
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $allowed = BackendUserGroupPermission::configured();
        // Only filter when configured
        if (!empty($allowed)) {
            $query->matching($query->in('uid', $allowed));
        }

        return $query->execute();
    }
}
