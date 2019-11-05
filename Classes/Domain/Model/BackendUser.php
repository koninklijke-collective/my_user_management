<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model for backend user
 */
class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{

    /**
     * @var string
     */
    public const TABLE = 'be_users';

    /** @var array */
    protected $inheritedMountPoints = [];

    /** @var array */
    protected $activeMountPoints = [];

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup> */
    protected $backendUserGroups;

    /**
     * Returns the Database Mount Points
     *
     * @param  bool  $exploded
     * @return array|string
     */
    public function getDbMountPoints($exploded = false)
    {
        if ($exploded) {
            return GeneralUtility::intExplode(',', $this->dbMountPoints, true);
        } else {
            return $this->dbMountPoints;
        }
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
     * @param  array  $allInheritedMountPoints
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
     * @param  array  $activePageMount
     * @return void
     */
    public function setActiveMountPoints(array $activePageMount)
    {
        $this->activeMountPoints = $activePageMount;
    }
}
