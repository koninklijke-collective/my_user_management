<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model for backend user
 */
final class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{
    public const TABLE = 'be_users';

    /** @var array */
    protected $_dbMountPoints;

    /** @var array */
    protected $inheritedMountPoints;

    /** @var array */
    protected $activeMountPoints;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup> */
    protected $backendUserGroups;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser */
    protected $createdBy;

    /**
     * @return int[]
     */
    public function getDatabaseMountPoints(): array
    {
        if ($this->_dbMountPoints === null) {
            $this->_dbMountPoints = GeneralUtility::intExplode(',', $this->dbMountPoints, true);
        }

        return $this->_dbMountPoints;
    }

    /**
     * Returns all generated MountPoints
     *
     * @return array
     */
    public function getInheritedMountPoints(): array
    {
        if ($this->inheritedMountPoints === null) {
            $mounts = $this->getDatabaseMountPoints();

            foreach ($this->getBackendUserGroups() as $group) {
                /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $group */
                $mounts = $this->getAllDatabaseMountsFromUserGroup($group, $mounts);
            }
            $this->setInheritedMountPoints($mounts);
        }

        return $this->inheritedMountPoints;
    }

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup  $group
     * @param  array  $mounts
     * @return array
     */
    protected function getAllDatabaseMountsFromUserGroup(BackendUserGroup $group, array $mounts = []): array
    {
        $dbMounts = $group->getDatabaseMountPoints();
        if (is_array($dbMounts)) {
            $mounts = array_unique(array_merge($mounts, $dbMounts));
        }

        if ($group->getSubGroups() !== null) {
            foreach ($group->getSubGroups() as $subGroup) {
                /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $subGroup */
                $mounts = $this->getAllDatabaseMountsFromUserGroup($subGroup, $mounts);
            }
        }

        return $mounts;
    }

    /**
     * Sets all inherited Mount Points
     *
     * @param  array  $allInheritedMountPoints
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser
     */
    public function setInheritedMountPoints(array $allInheritedMountPoints): BackendUser
    {
        $this->inheritedMountPoints = $allInheritedMountPoints;

        return $this;
    }

    /**
     * Returns the generated active page mounts
     *
     * @return array|null when not yet generated
     */
    public function getActiveMountPoints(): ?array
    {
        return $this->activeMountPoints;
    }

    /**
     * Sets the ActivePageMount
     *
     * @param  array  $activePageMount
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser
     */
    public function setActiveMountPoints(array $activePageMount): BackendUser
    {
        $this->activeMountPoints = $activePageMount;

        return $this;
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser|null
     */
    public function getCreatedBy(): ?BackendUser
    {
        return $this->createdBy;
    }

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser  $createdBy
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser
     */
    public function setCreatedBy(BackendUser $createdBy): BackendUser
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Override function to actually look into session table
     *
     * @return bool
     */
    public function isCurrentlyLoggedIn(): bool
    {
        return GeneralUtility::makeInstance(OnlineSessionService::class)
            ->userIsCurrentlyLoggedIn($this);
    }
}
