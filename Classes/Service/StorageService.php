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
    private StorageRepository $storageRepository;

    public function __construct(StorageRepository $storageRepository)
    {
        $this->storageRepository = $storageRepository;
    }

    /**
     * Retrieve path details from given id
     *
     * @param  int|ResourceStorage  $value
     * @param  string  $location
     * @return string
     */
    public function path($value, string $location = '/'): string
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

        if ($folder === null || $folder->getPublicUrl() === null) {
            return $storage->getUid() . ':' . ($location ?: '/');
        }

        return $folder->getPublicUrl();
    }

    /**
     * @param  int|ResourceStorage  $storage
     * @return ResourceStorage|null
     */
    private function getStorage($storage): ?ResourceStorage
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
