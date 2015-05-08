<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * Repository for \Serfhos\MyUserManagement\Domain\Model\BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    );

    /**
     * Initializes the repository.
     *
     * @return void
     */
    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setIgnoreEnableFields(true);
        $querySettings->setEnableFieldsToBeIgnored(array('hidden'));
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Finds Backend Users on a given list of uids
     *
     * @param array $uidList
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult<\Serfhos\MyUserManagement\Domain\Model\BackendUser>
     */
    public function findByUidList(array $uidList)
    {
        $query = $this->createQuery();
        return $query->matching($query->in('uid', $GLOBALS['TYPO3_DB']->cleanIntArray($uidList)))->execute();
    }

    /**
     * Finds all groups containing the specified file mount
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByFileMount(\Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                $query->equals('fileMountpoints', (int) $fileMount->getUid()),
                $query->like('fileMountpoints', (int) $fileMount->getUid() . ',%'),
                $query->like('fileMountpoints', '%,' . (int) $fileMount->getUid()),
                $query->like('fileMountpoints', '%,' . (int) $fileMount->getUid() . ',%')
            )
        );
        return $query->execute();
    }

    /**
     * Find all backend user groups by demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $demand
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByDemand(\Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $demand)
    {
        $query = $this->createQuery();
        $constraints = array();

        // Apply title filter
        if ($demand->getTitle()) {
            $constraints[] = $query->like('title', '%' . $demand->getTitle() . '%');
        }

        // Compile constraints
        if (count($constraints) > 0) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        return $query->execute();
    }
}