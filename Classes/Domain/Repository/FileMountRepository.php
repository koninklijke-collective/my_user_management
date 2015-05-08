<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * File mount repository
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FileMountRepository
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
     * Find file mounts by demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByDemand(\Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand)
    {
        $query = $this->createQuery();
        $constraints = array();

        // Filter by title
        if ($demand->getTitle()) {
            $constraints[] = $query->like('title', '%' . $demand->getTitle() . '%');
        }

        // Filter by path
        if ($demand->getPath()) {
            $constraints[] = $query->like('path', '%' . $demand->getPath() . '%');
        }

        // Add constraints
        if (count($constraints) > 0) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        return $query->execute();
    }
}