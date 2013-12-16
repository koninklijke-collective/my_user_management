<?php
namespace Serfhos\MyUserManagement\Service;
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
 * Module data storage service.
 * Used to store and retrieve module state (eg. checkboxes, selections).
 *
 * @package Serfhos\MyUserManagement\Service
 */
class ModuleDataStorageService implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var string
     */
    const PREFIX = 'tx_myusermanagement';

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Loads module data for user settings or returns a fresh object initially
     *
     * @param string $key
     * @return mixed
     */
    public function loadModuleData($key = '') {
        if (!isset($this->key)) $this->key = $key;

        $moduleData = $GLOBALS['BE_USER']->getModuleData(self::PREFIX . $this->key);
        if (empty($moduleData) || !$moduleData) {
			switch($this->key) {
				case '_backend_user_group':
					$moduleData = $this->objectManager->get('Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroupModuleData');
					break;
				case '_file_mount':
					$moduleData = $this->objectManager->get('Serfhos\\MyUserManagement\\Domain\\Model\\FileMountModuleData');
					break;
				case '_backend_user':
				default:
					$moduleData = $this->objectManager->get('TYPO3\\CMS\\Beuser\\Domain\\Model\\ModuleData');
					break;
			}
        } else {
            $moduleData = @unserialize($moduleData);
        }
        return $moduleData;
    }

	/**
	 * Sets the key
	 *
	 * @param string $key
	 * @return void
	 */
	public function setKey($key) {
		$this->key = $key;
	}

    /**
     * Persists serialized module data to user settings
     *
     * @param mixed $moduleData
     * @return void
     */
    public function persistModuleData($moduleData) {
        $GLOBALS['BE_USER']->pushModuleData(self::PREFIX . $this->key, serialize($moduleData));
    }

}
?>