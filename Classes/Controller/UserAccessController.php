<?php
namespace Serfhos\MyUserManagement\Controller;
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
 * User management controller
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class UserAccessController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var \TYPO3\CMS\Beuser\Domain\Model\ModuleData
     */
    protected $moduleData;

    /**
     * @var \Serfhos\MyUserManagement\Service\AccessService
     * @inject
     */
    protected $accessService;

    /**
     * @var \Serfhos\MyUserManagement\Service\ModuleDataStorageService
     * @inject
     */
    protected $moduleDataStorageService;

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $currentUser;

    /**
     * Selected page ID
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * Initialize used variables
     */
    public function __construct() {
        $this->currentUser = $GLOBALS['BE_USER'];
    }

    /**
     * Load and persist module data
     *
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
        $this->moduleData = $this->moduleDataStorageService->loadModuleData('_user_access');
        // We "finally" persist the module data.
        try {
            parent::processRequest($request, $response);
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
        } catch (\TYPO3\CMS\Extbase\Mvc\Exception\StopActionException $e) {
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
            throw $e;
        }
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
        $view->assignMultiple(array(
            'currentUser' => $this->currentUser,
            'returnUrl' => 'mod.php?M=MyUserManagementMyusermanagement_MyUserManagementUseraccess',
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'pageId' => $this->id,
        ));
    }

    /**
     * Initialize action parameters
     *
     * @return void
     */
    protected function initializeAction() {
        $this->id = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id'));
    }

    /**
     * Action: List users
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return void
     */
    public function listAction(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand = NULL) {
        if ($demand === NULL) {
            $demand = $this->moduleData->getDemand();
        } else {
            $this->moduleData->setDemand($demand);
        }

        $backendUsers = $this->accessService->findUsersWithPageAccess($this->id, $demand);
        $this->view->assignMultiple(array(
            'backendUsers' => $backendUsers,
            'demand' => $demand
        ));
    }

}
?>