<?php
namespace Serfhos\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model for backend user
 *
 * @package Serfhos\MyUserManagement\Domain\Model
 */
class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{

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
    public function getDbMountPoints()
    {
        return GeneralUtility::intExplode(',', $this->dbMountPoints, true);
    }

    /**
     * Returns all inherited Mount Points
     *
     * @return array
     */
    public function getInheritedMountPoints()
    {
        return $this->inheritedMountPoints;
    }

    /**
     * Sets all inherited Mount Points
     *
     * @param array $allInheritedMountPoints
     * @return void
     */
    public function setInheritedMountPoints(array $allInheritedMountPoints)
    {
        $this->inheritedMountPoints = $allInheritedMountPoints;
    }

    /**
     * Returns the ActivePageMount
     *
     * @return array
     */
    public function getActiveMountPoints()
    {
        return $this->activeMountPoints;
    }

    /**
     * Sets the ActivePageMount
     *
     * @param array $activePageMount
     * @return void
     */
    public function setActiveMountPoints(array $activePageMount)
    {
        $this->activeMountPoints = $activePageMount;
    }
}
