<?php
namespace Serfhos\MyUserManagement\Domain\Model;

/**
 * Model for backend user group
 *
 * @package Serfhos\MyUserManagement\Domain\Model
 */
class BackendUserGroup extends \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup
{

    /**
     * Flag for record being hidden
     *
     * @var boolean
     */
    protected $isDisabled;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Serfhos\MyUserManagement\Domain\Model\BackendUserGroup>
     */
    protected $subGroups;

    /**
     * @var string
     */
    protected $dbMountPoints = '';

    /**
     * The file mount points
     *
     * @var string
     */
    protected $fileMountpoints = '';

    /**
     * Sets the Database Mount Points
     *
     * @param string $dbMountPoints
     * @return void
     */
    public function setDbMountPoints($dbMountPoints)
    {
        $this->dbMountPoints = $dbMountPoints;
    }

    /**
     * Returns the Database Mount Points
     *
     * @return string
     */
    public function getDbMountPoints()
    {
        return $this->dbMountPoints;
    }

    /**
     * Sets the file mount points
     *
     * @param string $fileMountPoints
     * @return void
     */
    public function setFileMountpoints($fileMountPoints)
    {
        $this->fileMountpoints = $fileMountPoints;
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
     * Sets isDisabled
     *
     * @param boolean $isDisabled
     * @return void
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
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
}