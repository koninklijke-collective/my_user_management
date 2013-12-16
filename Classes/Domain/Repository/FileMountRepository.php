<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

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

/**
 * File mount repository
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FileMountRepository {

    /**
     * Initializes the repository.
     *
     * @return void
     */
    public function initializeObject() {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setIgnoreEnableFields(TRUE);
        $querySettings->setEnableFieldsToBeIgnored(array('hidden'));
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Find file mounts by demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByDemand(\Serfhos\MyUserManagement\Domain\Model\FileMountDemand $demand) {
        $query = $this->createQuery();
        $constraints = array();

        // Filter by title
        if ($demand->getTitle()) {
            $constraints[] = $query->like('title', '%' . $demand->getTitle() . '%');
        }

        // Filter by path
        if ($demand->getPath()) {
            $constraints[] = $query->like('path', '%' . $demand->getPath() . '%');
        }

        // Add constraints
        if (count($constraints) > 0) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        return $query->execute();
    }

}