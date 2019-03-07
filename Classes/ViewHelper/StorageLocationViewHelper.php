<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper: Storage Location
 *
 * @package KoninklijkeCollective\MyUserManagement\ViewHelpers
 */
class StorageLocationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper implements \TYPO3\CMS\Core\SingletonInterface
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
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('storageId', 'integer', 'Arguments', false);
        $this->registerArgument('location', 'string', 'Arguments', false);
    }

    /**
     * Retrieve page details from given page id
     *
     * @return string Rendered string
     */
    public function render()
    {
        $storageId = GeneralUtility::_GP('storageId');
        $location =  GeneralUtility::_GP('location') ? GeneralUtility::_GP('location'): '/';;

        $output = null;
        if (!isset($this->storage[$storageId]) || !($this->storage[$storageId] instanceof \TYPO3\CMS\Core\Resource\ResourceStorage)) {
            try {
                $this->storage[$storageId] = $this->storageRepository->findByUid($storageId);
            } catch (\Exception $e) {
            }
        }
        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
        $storage = $this->storage[$storageId];

        if ($storage instanceof \TYPO3\CMS\Core\Resource\ResourceStorage) {
            $folder = null;
            try {
                $folder = $storage->getFolder($location);
            } catch (\Exception $e) {
            }

            if ($folder instanceof \TYPO3\CMS\Core\Resource\Folder) {
                $output = $folder->getPublicUrl();
            }
        }

        if (empty($output)) {
            $output = $storageId . ': ' . $location;
        }

        return $output;
    }
}
