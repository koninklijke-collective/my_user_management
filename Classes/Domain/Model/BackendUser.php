<?php
namespace Serfhos\MyUserManagement\Domain\Model;
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
 * Model for backend user
 *
 * @package Serfhos\MyUserManagement\Domain\Model
 */
class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser {

    /**
     * @var array
     */
    protected $inheritedMountPoints = array();

    /**
     * @var array
     */
    protected $activeMountPoints = array();

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Serfhos\MyUserManagement\Domain\Model\BackendUserGroup>
     */
    protected $backendUserGroups;

    /**
     * Returns the Database Mount Points
     *
     * @return array
     */
    public function getDbMountPoints() {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->dbMountPoints, TRUE);
    }

    /**
     * Returns all inherited Mount Points
     *
     * @return array
     */
    public function getInheritedMountPoints() {
        return $this->inheritedMountPoints;
    }

    /**
     * Sets all inherited Mount Points
     *
     * @param array $allInheritedMountPoints
     * @return void
     */
    public function setInheritedMountPoints(array $allInheritedMountPoints) {
        $this->inheritedMountPoints = $allInheritedMountPoints;
    }

    /**
     * Sets the ActivePageMount
     *
     * @param array $activePageMount
     * @return void
     */
    public function setActiveMountPoints(array $activePageMount) {
        $this->activeMountPoints = $activePageMount;
    }

    /**
     * Returns the ActivePageMount
     *
     * @return array
     */
    public function getActiveMountPoints() {
        return $this->activeMountPoints;
    }

}
?>