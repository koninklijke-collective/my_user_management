<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Model for backend user
 */
final class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{
    public const TABLE = 'be_users';

    protected ?array $_dbMountPoints = null;

    protected ?array $inheritedMountPoints = null;

    protected ?array $activeMountPoints = null;

    protected ?BackendUser $createdBy = null;

    /**
     * Override default backend user groups to map own custom model
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup>
     */
    protected ObjectStorage $backendUserGroups;

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
     * @param  array $groups
     * @return array
     */
    protected function getAllDatabaseMountsFromUserGroup(BackendUserGroup $group, array $mounts = [], array $groups = []): array
    {
        $dbMounts = $group->getDatabaseMountPoints();
        $mounts = array_unique(array_merge($mounts, $dbMounts));

        // Stock group UIDs to prevent infinite loop in case of subgroup circular references.
        $groups[] = $group->getUid();

        if ($group->getSubGroups() !== null) {
            foreach ($group->getSubGroups() as $subGroup) {
                if (!in_array($subGroup->getUid(), $groups, true)) {
                    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $subGroup */
                    $mounts = $this->getAllDatabaseMountsFromUserGroup($subGroup, $mounts, $groups);
                }
            }
        }

        return $mounts;
    }

    public function setInheritedMountPoints(array $allInheritedMountPoints): BackendUser
    {
        $this->inheritedMountPoints = $allInheritedMountPoints;

        return $this;
    }

    public function getActiveMountPoints(): ?array
    {
        return $this->activeMountPoints;
    }

    public function setActiveMountPoints(array $activePageMount): BackendUser
    {
        $this->activeMountPoints = $activePageMount;

        return $this;
    }

    public function getCreatedBy(): ?BackendUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(BackendUser $createdBy): BackendUser
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Override function to actually look into session table
     */
    public function isCurrentlyLoggedIn(): bool
    {
        return GeneralUtility::makeInstance(OnlineSessionService::class)
            ->userIsCurrentlyLoggedIn($this);
    }
}
