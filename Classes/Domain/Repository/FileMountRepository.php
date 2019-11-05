<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Repository: FileMount
 */
final class FileMountRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FileMountRepository
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
    public function initializeObject(): void
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setIgnoreEnableFields(true);
        $querySettings->setEnableFieldsToBeIgnored(['hidden']);
        $this->setDefaultQuerySettings($querySettings);
    }
}
