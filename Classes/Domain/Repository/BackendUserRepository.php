<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

final class BackendUserRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
{
    use BackendUserAuthenticationTrait;

    protected $defaultOrderings = ['username' => QueryInterface::ORDER_ASCENDING];

    public function findByUid($uid): ?BackendUser
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
    }

    /**
     * Override demanded query for filtering by group access
     */
    public function findDemanded(Demand $demand)
    {
        $result = parent::findDemanded($demand);

        // Do query again with configured permissions applied
        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            $query = $result->getQuery();
            $this->applyUserGroupPermission($query);

            return $query->execute();
        }

        return $result;
    }

    public function findAllActive(): array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(
            $query->equals('deleted', false),
            $query->equals('disable', false),
        ));

        $this->applyUserGroupPermission($query);

        return $query->execute();
    }

    public function findAllInactive(DateTime $lastLoginSince): array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(
            $query->equals('deleted', false),
            $query->lessThanOrEqual('lastlogin', $lastLoginSince),
        ));

        $query = $this->applyUserGroupPermission($query);

        return $query->execute();
    }

    public function applyUserGroupPermission(QueryInterface $query): QueryInterface
    {
        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            $constraints = [
                $query->getConstraint(),
                $query->logicalNot($query->like('username', '_cli_%')),
            ];

            if (BackendUserGroupPermission::hasConfigured()) {
                $allowedConstraints = [
                    // Always allow current user
                    $query->equals('uid', $this->getBackendUserAuthentication()->user['uid']),
                ];
                foreach (BackendUserGroupPermission::getConfigured() as $id) {
                    // @TODO: Refactor for real n:m relations
                    $allowedConstraints[] = $query->logicalOr(
                        $query->equals('usergroup', (int)$id),
                        $query->like('usergroup', (int)$id . ',%'),
                        $query->like('usergroup', '%,' . (int)$id),
                        $query->like('usergroup', '%,' . (int)$id . ',%'),
                    );
                }
                $constraints[] = $query->logicalOr(...$allowedConstraints);
            }

            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query;
    }
}
