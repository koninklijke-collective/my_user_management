<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * User management controller
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class BackendUserController extends AbstractBackendController
{

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
     * Initialize the action menu items
     *
     * @return void
     */
    protected function initializeActionMenu()
    {
        $this->actionMenuItems = array(
            array(
                'controller' => 'BackendUser',
                'action' => 'list',
                'labelKey' => 'backendUsers',
                'defaultLabel' => '',
            ),
            array(
                'controller' => 'BackendUser',
                'action' => 'online',
                'labelKey' => 'onlineUsers',
                'defaultLabel' => '',
            )
        );
    }

    /**
     * Action: List users
     *
     * @param \TYPO3\CMS\Beuser\Domain\Model\Demand $demand
     * @return void
     */
    public function listAction(\TYPO3\CMS\Beuser\Domain\Model\Demand $demand = null)
    {
        if ($demand === null) {
            $demand = $this->moduleData->getDemand();
        } else {
            $this->moduleData->setDemand($demand);
        }
        // Switch user permanently or only until logout
        if (GeneralUtility::_GP('SwitchUser')) {
            $this->administrationService->switchUser(
                GeneralUtility::_GP('SwitchUser'),
                true
            );
        }
        $compareUserList = $this->moduleData->getCompareUserList();
        $this->view->assignMultiple(array(
            'demand' => $demand,
            'backendUsers' => $this->backendUserRepository->findDemanded($demand),
            'compareUserList' => (!empty($compareUserList) ? $this->backendUserRepository->findByUidList($compareUserList) : ''),
            'backendUserGroups' => $this->backendUserGroupRepository->findAll(),
        ));
    }

    /**
     * Action: Display all online editors
     *
     * @return void
     */
    public function onlineAction()
    {
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
    public function compareAction()
    {
        $compareUserList = $this->moduleData->getCompareUserList();
        $this->view->assign('compareUserList',
            !empty($compareUserList) ? $this->backendUserRepository->findByUidList($compareUserList) : '');
    }

    /**
     * Attaches one backend user to the compare list
     *
     * @param integer $uid
     * @retun void
     */
    public function addToCompareListAction($uid)
    {
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
    public function removeFromCompareListAction($uid)
    {
        $this->moduleData->detachUidCompareUser($uid);
        $this->moduleDataStorageService->persistModuleData($this->moduleData);
        $this->forward('list');
    }

    /**
     * Returns generic module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return 'MyUserManagementMyusermanagement_MyUserManagementUseradmin';
    }
}