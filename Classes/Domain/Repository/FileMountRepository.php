<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<\KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount>
 */
final class FileMountRepository extends Repository
{
    use BackendUserAuthenticationTrait;

    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'identifier' => QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject(): void
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $querySettings->setIgnoreEnableFields(true);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function createQuery(): QueryInterface
    {
        $query = parent::createQuery();
        $user = self::getBackendUserAuthentication();
        if ($user->isAdmin()) {
            return $query;
        }

        $ids = [-1];
        foreach ($user->getFileMountRecords() as $record) {
            $ids[] = $record['uid'] ?? null;
        }
        $ids = array_filter($ids);

        return $query->matching($query->in('uid', $ids));
    }
}
