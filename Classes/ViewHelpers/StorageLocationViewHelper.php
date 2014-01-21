<?php
namespace Serfhos\MyUserManagement\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Benjamin Serfhos <serfhos@serfhos.com>,
 *  Rotterdam School of Management, Erasmus University
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * ViewHelper: Storage Location
 *
 * @package Serfhos\MyUserManagement\ViewHelpers
 */
class StorageLocationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * Cached storages
     *
     * @var array
     */
    protected $storage = array();

    /**
     * @var \TYPO3\CMS\Core\Resource\StorageRepository
     * @inject
     */
    protected $storageRepository;

    /**
     * Retrieve page details from given page id
     *
     * @param integer $storageId
     * @param string $location
     * @return string Rendered string
     */
    public function render($storageId, $location = '/') {
        $output = NULL;
        if (!isset($this->storage[$storageId]) || !($this->storage[$storageId] instanceof \TYPO3\CMS\Core\Resource\ResourceStorage)) {
            try {
                $this->storage[$storageId] = $this->storageRepository->findByUid($storageId);
            } catch (\Exception $e) {}
        }
        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
        $storage = $this->storage[$storageId];

        if ($storage instanceof \TYPO3\CMS\Core\Resource\ResourceStorage) {
            $folder = NULL;
            try {
                $folder = $storage->getFolder($location);
            } catch (\Exception $e) {}

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
?>