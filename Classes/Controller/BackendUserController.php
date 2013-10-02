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
class BackendUserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var \TYPO3\CMS\Beuser\Domain\Model\ModuleData
     */
    protected $moduleData;

    /**
     * @var \Serfhos\MyUserManagement\Service\ModuleDataStorageService
     * @inject
     */
    protected $moduleDataStorageService;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * @var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository
     * @inject
     */
    protected $backendUserSessionRepository;

    /**
     * @var \Serfhos\MyUserManagement\Service\AdministrationService
     * @inject
     */
    protected $administrationService;

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $backEndUser;

    /**
     * Initialize used variables
     */
    public function __construct() {
        $this->backEndUser = $GLOBALS['BE_USER'];
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
        $view->assignMultiple(array(
            'currentUser' => $this->backEndUser,
            'returnUrl' => 'mod.php?M=MyUserManagementMyusermanagement_MyUserManagementUseradmin',
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'backendUserGroups' => array_merge(array(''), $this->backendUserGroupRepository->findAll()->toArray()),
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
        $this->moduleData = $this->moduleDataStorageService->loadModuleData('_backend_user');
        if (!$this->backEndUser->isAdmin()) {
            // force module data to only display non-admin users
            $demand = $this->moduleData->getDemand();
            $demand->setUserType(2);
            $this->moduleData->setDemand($demand);
        }

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
     * Initialize actions
     *
     * @return void
     * @throws \RuntimeException
     */
    public function initializeAction() {
        // @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
        // and settings. Thus, we need a TypoScript root template, that then loads the
        // ext_typoscript_setup.txt file of this module. This is nasty, but can not be
        // circumvented until there is a better solution in extbase.
        // For now we throw an exception if no settings are detected.
        if (empty($this->settings)) {
            throw new \RuntimeException('No settings detected. This module can not work then. This usually happens if there is no frontend TypoScript template with root flag set. ' . 'Please create a frontend page with a TypoScript root template.', 1344375003);
        }
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
        // Switch user permanently or only until logout
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('SwitchUser')) {
            $this->administrationService->switchUser(
                \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('SwitchUser'),
                TRUE
            );
        }
        $compareUserList = $this->moduleData->getCompareUserList();
        $this->view->assign('demand', $demand);
        $this->view->assign('backendUsers', $this->backendUserRepository->findDemanded($demand));
        $this->view->assign('compareUserList', !empty($compareUserList) ? $this->backendUserRepository->findByUidList($compareUserList) : '');
    }

    /**
     * Action: Display all online editors
     *
     * @return void
     */
    public function onlineAction() {
        $onlineUsersAndSessions = array();
        $onlineUsers = $this->backendUserRepository->findOnline();
        foreach ($onlineUsers as $onlineUser) {
            $onlineUsersAndSessions[] = array(
                'backendUser' => $onlineUser,
                'sessions' => $this->backendUserSessionRepository->findByBackendUser($onlineUser)
            );
        }
        $this->view->assign('onlineUsersAndSessions', $onlineUsersAndSessions);
        $this->view->assign('currentSessionId', $GLOBALS['BE_USER']->user['ses_id']);
    }

    /**
     * Compare backend users from demand
     *
     * @return void
     */
    public function compareAction() {
        $compareUserList = $this->moduleData->getCompareUserList();
        $this->view->assign('dateFormat', $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy']);
        $this->view->assign('timeFormat', $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']);
        $this->view->assign('compareUserList', !empty($compareUserList) ? $this->backendUserRepository->findByUidList($compareUserList) : '');
    }

    /**
     * Attaches one backend user to the compare list
     *
     * @param integer $uid
     * @retun void
     */
    public function addToCompareListAction($uid) {
        $this->moduleData->attachUidCompareUser($uid);
        $this->moduleDataStorageService->persistModuleData($this->moduleData);
        $this->forward('list');
    }

    /**
     * Removes given backend user to the compare list
     *
     * @param integer $uid
     * @retun void
     */
    public function removeFromCompareListAction($uid) {
        $this->moduleData->detachUidCompareUser($uid);
        $this->moduleDataStorageService->persistModuleData($this->moduleData);
        $this->forward('list');
    }

}
?>