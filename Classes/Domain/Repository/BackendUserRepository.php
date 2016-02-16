<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * Repository: BackendUser
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
{

    protected static $systemUsers = array('_cli_lowlevel', '_cli_scheduler');


    /**
     * @var Serfhos\MyUserManagement\Service\RestrictBackendUserGroupService
     * @inject
     */
    protected $restrictBackendUserGroupService;

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'username' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    );

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
        return $query->execute();
    }

    /**
     * Find Backend Users matching to Demand object properties
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult<\TYPO3\CMS\Beuser\Domain\Model\BackendUser>
     */
    public function findDemanded(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand)
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult */
        $objects = parent::findDemanded($demand);

        /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $objects->getQuery();

        // Do not list system low level users for non admins.
        if ($this->getBackendUserAuthentication()->isAdmin() === false && $objects instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult) {
            $query->matching(
                $query->logicalAnd(
                    $query->getConstraint(),
                    $query->logicalNot(
                        $query->in('username', static::$systemUsers)
                    )
                )
            );
        }

        // Limit listed users to choosen groups from custom permission.
        $groups = $this->restrictBackendUserGroupService->getRestrictedBackendUserGroups();
        if (count($groups)) {
            $query->matching(
                $query->logicalAnd(
                    $query->getConstraint(),
                    $query->in('usergroup', $groups)
                )
            );
        }

        return $query->execute();
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}