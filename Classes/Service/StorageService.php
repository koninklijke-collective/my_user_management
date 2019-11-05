<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class StorageService implements SingletonInterface
{

    /** @var array */
    protected $storage = [];

    /**
     * @var \TYPO3\CMS\Core\Resource\StorageRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $storageRepository;

    /**
     * Retrieve path details from given id
     *
     * @param  int|\TYPO3\CMS\Core\Resource\ResourceStorage  $value
     * @param  string  $location
     * @return string
     */
    public function path($value, $location = '/'): string
    {
        $storage = $this->getStorage($value);
        if ($storage === null) {
            return $value . ':' . $location;
        }

        $folder = null;
        try {
            $folder = $storage
                ->getFolder($location);
        } catch (\Exception $e) {
        }

        if ($folder === null) {
            return $storage->getUid() . ':' . $location;
        }

        return $folder->getPublicUrl();
    }

    /**
     * @param  int|\TYPO3\CMS\Core\Resource\ResourceStorage  $storage
     * @return \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected function getStorage($storage): ?ResourceStorage
    {
        if ($storage instanceof ResourceStorage) {
            return $this->storage[$storage->getUid()] = $storage;
        }

        if (MathUtility::canBeInterpretedAsInteger($storage)) {
            $storageId = (int)$storage;

            if ($this->storage[$storageId] !== null) {
                return $this->storage[$storageId];
            }

            return $this->storage[$storageId] = $this->getStorageRepository()->findByIdentifier($storageId);
        }

        return null;
    }

    /**
     * @return \TYPO3\CMS\Core\Resource\StorageRepository
     */
    protected function getStorageRepository(): StorageRepository
    {
        if ($this->storageRepository === null) {
            $this->storageRepository = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(StorageRepository::class);
        }

        return $this->storageRepository;
    }
}
