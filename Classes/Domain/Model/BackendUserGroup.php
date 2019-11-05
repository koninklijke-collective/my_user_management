<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom model for backend user group readability
 */
final class BackendUserGroup extends \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup
{
    public const TABLE = 'be_groups';

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup> */
    protected $subGroups;

    /** @var string */
    protected $databaseMountPoints = '';
    /** @var array exploded from $databaseMountPoints */
    protected $_databaseMountPoints;

    /** @var string */
    protected $fileMountPoints = '';
    /** @var array exploded from $fileMountPoint */
    protected $_fileMountPoints;

    /**
     * @return array
     */
    public function getDatabaseMountPoints(): array
    {
        if ($this->_databaseMountPoints === null) {
            $this->_databaseMountPoints = GeneralUtility::intExplode(',', $this->databaseMountPoints, true);
        }

        return $this->_databaseMountPoints;
    }

    /**
     * @param  string  $databaseMountPoints
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup
     */
    public function setDatabaseMountPoints(string $databaseMountPoints): BackendUserGroup
    {
        $this->databaseMountPoints = $databaseMountPoints;
        $this->_databaseMountPoints = GeneralUtility::intExplode(',', $databaseMountPoints, true);

        return $this;
    }

    /**
     * @return array
     */
    public function getFileMountPoints(): array
    {
        if ($this->_fileMountPoints === null) {
            $this->_fileMountPoints = GeneralUtility::intExplode(',', $this->fileMountPoints, true);
        }

        return $this->_fileMountPoints;
    }

    /**
     * @param  string  $fileMountPoints
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup
     */
    public function setFileMountPoints(string $fileMountPoints): BackendUserGroup
    {
        $this->fileMountPoints = $fileMountPoints;
        $this->_fileMountPoints = GeneralUtility::intExplode(',', $fileMountPoints, true);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisabled(): bool
    {
        return $this->hidden === true;
    }
}
