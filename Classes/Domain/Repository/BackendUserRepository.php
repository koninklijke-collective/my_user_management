<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * Repository: BackendUser
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
{

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
}