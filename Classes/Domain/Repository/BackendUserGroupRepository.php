<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Repository: BackendUserGroup
 */
final class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{

    protected $configuredBackendUserGroups = [];

    public function __construct(ObjectManagerInterface $objectManager)
    {
        parent::__construct($objectManager);
        $this->configuredBackendUserGroups = BackendUserGroupPermission::getConfigured();
    }


    public function createQuery(): \TYPO3\CMS\Extbase\Persistence\QueryInterface
    {
        $query = parent::createQuery();
        if (!empty($this->configuredBackendUserGroups)) {
            $query->matching($query->logicalAnd([
                $query->in('uid', BackendUserGroupPermission::getConfigured()),
            ]));
        }
        return $query;
    }
}
