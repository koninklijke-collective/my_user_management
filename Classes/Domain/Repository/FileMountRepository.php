<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Repository: FileMount
 */
class FileMountRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FileMountRepository
{

    /** @var array */
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'path' => QueryInterface::ORDER_ASCENDING,
    ];

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
        $querySettings->setEnableFieldsToBeIgnored(['hidden']);
        $this->setDefaultQuerySettings($querySettings);
    }
}
