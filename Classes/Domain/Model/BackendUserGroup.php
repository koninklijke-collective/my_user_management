<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom model for backend user group readability
 */
final class BackendUserGroup extends \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup
{
    public const TABLE = 'be_groups';

    protected string $databaseMountPoints = '';
    protected ?array $_databaseMountPoints = null;
    protected string $fileMountPoints = '';
    protected ?array $_fileMountPoints = null;

    /**
     * Override default sub groups to map own custom model
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup>
     */
    protected $subGroups;

    public function getDatabaseMountPoints(): array
    {
        if ($this->_databaseMountPoints === null) {
            $this->_databaseMountPoints = GeneralUtility::intExplode(',', $this->databaseMountPoints, true);
        }

        return $this->_databaseMountPoints;
    }

    public function setDatabaseMountPoints(string $databaseMountPoints): BackendUserGroup
    {
        $this->databaseMountPoints = $databaseMountPoints;
        $this->_databaseMountPoints = GeneralUtility::intExplode(',', $databaseMountPoints, true);

        return $this;
    }

    public function getFileMountPoints(): array
    {
        if ($this->_fileMountPoints === null) {
            $this->_fileMountPoints = GeneralUtility::intExplode(',', $this->fileMountPoints, true);
        }

        return $this->_fileMountPoints;
    }

    public function setFileMountPoints(string $fileMountPoints): BackendUserGroup
    {
        $this->fileMountPoints = $fileMountPoints;
        $this->_fileMountPoints = GeneralUtility::intExplode(',', $fileMountPoints, true);

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->hidden === true;
    }
}
