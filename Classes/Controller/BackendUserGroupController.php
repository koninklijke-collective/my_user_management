<?php
namespace Serfhos\MyUserManagement\Controller;

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
 * Class BackendGroupController.php
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class BackendUserGroupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * Backend user (for IDE)
	 *
	 * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected $backendUser;

	/**
	 * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
	 * @inject
	 */
	protected $backendUserGroupRepository;

	/**
	 * @var \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupModuleData
	 */
	protected $moduleData;

	/**
	 * @var \Serfhos\MyUserManagement\Service\ModuleDataStorageService
	 * @inject
	 */
	protected $moduleDataStorageService;

	/**
	 * Initialize used variables
	 */
	public function __construct() {
		$this->backendUser = $GLOBALS['BE_USER'];
	}

	/**
	 * Initialize actions
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	public function initializeAction() {
		/**
 		 * @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
		 * and settings. Thus, we need a TypoScript root template, that then loads the
		 * ext_typoscript_setup.txt file of this module. This is nasty, but can not be
		 * circumvented until there is a better solution in extbase.
		 * For now we throw an exception if no settings are detected.
		 */
		if (empty($this->settings)) {
			throw new \RuntimeException('No settings detected. This module can not work then. This usually happens if there is no frontend TypoScript template with root flag set. ' . 'Please create a frontend page with a TypoScript root template.', 1344375003);
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
			'currentUser' => $this->backendUser,
			'returnUrl' => 'mod.php?M=MyUserManagementMyusermanagement_MyUserManagementGroupadmin',
			'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
		));
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
		$this->moduleData = $this->moduleDataStorageService->loadModuleData('_backend_user_group');

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
	 * List all backend groups
	 *
	 * @return void
	 */
	public function listAction() {
		$compareBackendUserGroupList = $this->moduleData->getCompareGroupList();
		$this->view->assignMultiple(
			array(
				'backendUserGroups' => $this->backendUserGroupRepository->findAll(),
				'compareBackendUserGroupList' => !empty($compareBackendUserGroupList) ? $this->backendUserGroupRepository->findByUidList($compareBackendUserGroupList) : '',
			)

		);
	}

	/**
	 * Compare backend groups
	 *
	 * @return void
	 */
	public function compareAction() {
		$compareBackendUserGroupList = $this->moduleData->getCompareGroupList();
		$this->view->assign('compareBackendUserGroupList', $this->backendUserGroupRepository->findByUidList($compareBackendUserGroupList));
	}

	/**
	 * Attaches one backend user group to the compare list
	 *
	 * @param integer $uid
	 * @retun void
	 */
	public function addToCompareListAction($uid) {
		$this->moduleData->attachUidCompareGroup($uid);
		$this->moduleDataStorageService->persistModuleData($this->moduleData);
		$this->forward('list');
	}

	/**
	 * Removes given backend user group to the compare list
	 *
	 * @param integer $uid
	 * @retun void
	 */
	public function removeFromCompareListAction($uid) {
		$this->moduleData->detachUidCompareGroup($uid);
		$this->moduleDataStorageService->persistModuleData($this->moduleData);
		$this->forward('list');
	}

}