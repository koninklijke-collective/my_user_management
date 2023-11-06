<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use Exception;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\MathUtility;

final class StorageService implements SingletonInterface
{
    private array $storage = [];

    public function __construct(private StorageRepository $storageRepository)
    {
    }

    /**
     * Retrieve path details from given id
     */
    public function path(ResourceStorage|string|int $value, string $location = '/'): string
    {
        $storage = $this->getStorage($value);
        if ($storage === null) {
            return $value . ':' . $location;
        }

        $folder = null;
        try {
            $folder = $storage->getFolder($location);
        } catch (Exception $e) {
        }

        if ($folder?->getPublicUrl() === null) {
            return $value . ':' . $location;
        }

        return $folder->getPublicUrl();
    }

    private function getStorage(ResourceStorage|string|int $storage): ?ResourceStorage
    {
        if ($storage instanceof ResourceStorage) {
            return $this->storage[$storage->getUid()] = $storage;
        }

        if (MathUtility::canBeInterpretedAsInteger($storage)) {
            $storageId = (int)$storage;

            return $this->storage[$storageId] ?? ($this->storage[$storageId] = $this->storageRepository->findByUid($storageId));
        }

        return null;
    }
}
