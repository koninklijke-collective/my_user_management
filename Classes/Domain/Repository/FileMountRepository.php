<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository: FileMount
 */
final class FileMountRepository extends Repository
{
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
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class)
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['hidden']);
        $this->setDefaultQuerySettings($querySettings);
    }
}
