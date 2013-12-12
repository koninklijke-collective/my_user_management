<?php
namespace Serfhos\MyUserManagement\Domain\Model;

/***************************************************************
 * Copyright notice
 *
 * (c) 2013 Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 *  
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * File mount module data
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountModuleData {

	/**
	 * The demand
	 *
	 * @var \Serfhos\MyUserManagement\Domain\Model\FileMountDemand
	 */
	protected $demand;

	/**
	 * Gets the demand
	 *
	 * @return FileMountDemand
	 */
	public function getDemand() {
		if ($this->demand === NULL) {
			$this->demand = GeneralUtility::makeInstance('Serfhos\\MyUserManagement\\Domain\\Model\\FileMountDemand');
		}
		return $this->demand;
	}

	/**
	 * Sets the demand
	 *
	 * @param FileMountDemand $demand
	 * @return void
	 */
	public function setDemand(\Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand) {
		$this->demand = $demand;
	}

}