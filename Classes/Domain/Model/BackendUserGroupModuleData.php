<?php
namespace Serfhos\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Backend user group module data
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class BackendUserGroupModuleData
{

    /**
     * @var array
     */
    protected $compareGroupList = array();

    /**
     * The demand
     *
     * @var \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand
     */
    protected $demand;

    /**
     * Returns the compare list as array of user uis
     *
     * @return array
     */
    public function getCompareGroupList()
    {
        return array_keys($this->compareGroupList);
    }

    /**
     * Adds one backend user group (by uid) to the compare user group list
     * Cannot be ObjectStorage, must be array
     *
     * @param integer $uid
     * @return void
     */
    public function attachUidCompareGroup($uid)
    {
        $this->compareGroupList[$uid] = true;
    }

    /**
     * Strip one backend user group from the compare user list
     *
     * @param integer $uid
     * @return void
     */
    public function detachUidCompareGroup($uid)
    {
        unset($this->compareGroupList[$uid]);
    }

    /**
     * Sets the demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $demand
     * @return void
     */
    public function setDemand($demand)
    {
        $this->demand = $demand;
    }

    /**
     * Gets the demand
     *
     * @return \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand
     */
    public function getDemand()
    {
        if (!($this->demand instanceof \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand)) {
            $this->demand = GeneralUtility::makeInstance('Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroupDemand');
        }
        return $this->demand;
    }
}