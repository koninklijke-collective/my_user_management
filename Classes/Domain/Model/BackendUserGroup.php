<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model for backend user group
 */
class BackendUserGroup extends \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup
{

    /**
     * @var string
     */
    public const TABLE = 'be_groups';

    /**
     * Flag for record being hidden
     *
     * @var boolean
     */
    protected $isDisabled;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup> */
    protected $subGroups;

    /** @var string */
    protected $dbMountPoints = '';

    /**
     * The file mount points
     *
     * @var string
     */
    protected $fileMountpoints = '';

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
     * Sets the Database Mount Points
     *
     * @param  string  $dbMountPoints
     * @return void
     */
    public function setDbMountPoints($dbMountPoints)
    {
        $this->dbMountPoints = $dbMountPoints;
    }

    /**
     * Gets the file mount points
     *
     * @return string
     */
    public function getFileMountpoints()
    {
        return $this->fileMountpoints;
    }

    /**
     * Sets the file mount points
     *
     * @param  string  $fileMountPoints
     * @return void
     */
    public function setFileMountpoints($fileMountPoints)
    {
        $this->fileMountpoints = $fileMountPoints;
    }

    /**
     * Gets isDisabled
     *
     * @return boolean
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Sets isDisabled
     *
     * @param  boolean  $isDisabled
     * @return void
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }
}
