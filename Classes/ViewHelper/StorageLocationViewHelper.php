<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use Exception;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper: Storage Location
 */
class StorageLocationViewHelper extends AbstractViewHelper implements SingletonInterface
{

    /**
     * Cached storages
     *
     * @var array
     */
    protected $storage = [];

    /**
     * @var \TYPO3\CMS\Core\Resource\StorageRepository
     * @inject
     */
    protected $storageRepository;

    /**
     * Retrieve page details from given page id
     *
     * @param  integer  $storageId
     * @param  string  $location
     * @return string Rendered string
     */
    public function render($storageId, $location = '/')
    {
        $output = null;
        if (!isset($this->storage[$storageId]) || !($this->storage[$storageId] instanceof ResourceStorage)) {
            try {
                $this->storage[$storageId] = $this->storageRepository->findByUid($storageId);
            } catch (Exception $e) {
            }
        }
        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
        $storage = $this->storage[$storageId];

        if ($storage instanceof ResourceStorage) {
            $folder = null;
            try {
                $folder = $storage->getFolder($location);
            } catch (Exception $e) {
            }

            if ($folder instanceof Folder) {
                $output = $folder->getPublicUrl();
            }
        }

        if (empty($output)) {
            $output = $storageId . ': ' . $location;
        }

        return $output;
    }
}
