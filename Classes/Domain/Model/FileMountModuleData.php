<?php
namespace Serfhos\MyUserManagement\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * File mount module data
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountModuleData
{

    /**
     * The demand
     *
     * @var \Serfhos\MyUserManagement\Domain\Model\FileMountDemand
     */
    protected $demand;

    /**
     * Gets the demand
     *
     * @return \Serfhos\MyUserManagement\Domain\Model\FileMountDemand
     */
    public function getDemand()
    {
        if ($this->demand === null) {
            $this->demand = GeneralUtility::makeInstance('Serfhos\\MyUserManagement\\Domain\\Model\\FileMountDemand');
        }
        return $this->demand;
    }

    /**
     * Sets the demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand
     * @return void
     */
    public function setDemand(FileMountDemand $demand)
    {
        $this->demand = $demand;
    }
}