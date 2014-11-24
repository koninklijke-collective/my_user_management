<?php
namespace Serfhos\MyUserManagement\Domain\Repository;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Benjamin Serfhos <serfhos@serfhos.com>,
 *  Rotterdam School of Management, Erasmus University
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Repository for \Serfhos\MyUserManagement\Domain\Model\BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository {


    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    );

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
     * Finds Backend Users on a given list of uids
     *
     * @param array $uidList
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult<\Serfhos\MyUserManagement\Domain\Model\BackendUser>
     */
    public function findByUidList(array $uidList) {
        $query = $this->createQuery();
        return $query->matching($query->in('uid', $GLOBALS['TYPO3_DB']->cleanIntArray($uidList)))->execute();
    }

    /**
     * Finds all groups containing the specified file mount
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByFileMount(\Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                $query->equals('fileMountpoints', intval($fileMount->getUid())),
                $query->like('fileMountpoints', intval($fileMount->getUid()) . ',%'),
                $query->like('fileMountpoints', '%,' . intval($fileMount->getUid())),
                $query->like('fileMountpoints', '%,' . intval($fileMount->getUid()) . ',%')
            )
        );
        return $query->execute();
    }

    /**
     * Find all backend user groups by demand
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $demand
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByDemand(\Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $demand) {
        $query = $this->createQuery();
        $constraints = array();

        // Apply title filter
        if ($demand->getTitle()) {
            $constraints[] = $query->like('title', '%' . $demand->getTitle() . '%');
        }

        // Compile constraints
        if (count($constraints) > 0) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        return $query->execute();
    }

}