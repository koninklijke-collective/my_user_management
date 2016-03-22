<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

use Serfhos\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;

/**
 * Repository: BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{

    /**
     * @var string
     */
    const TABLE = 'be_groups';

    /**
     * Returns all allowed objects of this repository
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $allowed = BackendUserGroupPermission::userAllowed();
        // Only filter when configured
        if (!empty($allowed)) {
            $query->matching($query->in('uid', $allowed));
        }
        return $query->execute();
    }

}
