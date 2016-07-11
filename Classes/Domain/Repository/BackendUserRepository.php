<?php
namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

/**
 * Repository: BackendUser
 *
 * @package KoninklijkeCollective\MyUserManagement\Domain\Repository
 */
class BackendUserRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
{

    /**
     * @var string
     */
    const TABLE = 'be_users';

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'username' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    );

    /**
     * Override demanded query for filtering by group access
     */
    public function findDemanded(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand)
    {
        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $query = parent::findDemanded($demand)->getQuery();
            $this->applyUserGroupPermission($query);
            return $query->execute();
        } else {
            return parent::findDemanded($demand);
        }
    }

    /**
     * Find all active backend users
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllActive()
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(array(
            $query->equals('deleted', false),
            $query->equals('disable', false)
        )));
        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $this->applyUserGroupPermission($query);
        }
        return $query->execute();
    }

    /**
     * Find all inactive users based on last login
     *
     * @param \DateTime $lastLoginSince
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllInactive(\DateTime $lastLoginSince)
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(array(
            $query->equals('deleted', false),
            $query->equals('disable', false),
            $query->lessThanOrEqual('lastlogin', $lastLoginSince)
        )));
        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $this->applyUserGroupPermission($query);
        }
        return $query->execute();
    }

    /**
     * Apply allowed usergroups based on current logged in user
     *
     * @param $query \TYPO3\CMS\Extbase\Persistence\QueryInterface
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function applyUserGroupPermission($query)
    {
        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $constraints = array(
                $query->getConstraint(),
                $query->logicalNot($query->like('username', '_cli_%')),
            );
            $allowed = \KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission::userAllowed();
            if (!empty($allowed)) {
                $allowedConstraints = array(
                    // Always allow current user
                    $query->equals('uid', $this->getBackendUserAuthentication()->user['uid'])
                );
                foreach ($allowed as $id) {
                    // @TODO: Refactor for real n:m relations
                    $allowedConstraints[] = $query->logicalOr(array(
                        $query->equals('usergroup', (int) $id),
                        $query->like('usergroup', (int) $id . ',%'),
                        $query->like('usergroup', '%,' . (int) $id),
                        $query->like('usergroup', '%,' . (int) $id . ',%')
                    ));
                }
                $constraints[] = $query->logicalOr($allowedConstraints);
            }

            $query->matching($query->logicalAnd($constraints));
        }

        return $query;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

}
