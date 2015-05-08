<?php
namespace Serfhos\MyUserManagement\Controller;

/**
 * Class BackendGroupController.php
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class BackendUserGroupController extends AbstractBackendController
{

    /**
     * Override used class
     *
     * @var \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupModuleData
     */
    protected $moduleData;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * List all (demanded) backend groups
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $backendUserGroupDemand
     * @return void
     */
    public function listAction(
        \Serfhos\MyUserManagement\Domain\Model\BackendUserGroupDemand $backendUserGroupDemand = null
    ) {
        if ($backendUserGroupDemand === null) {
            $backendUserGroupDemand = $this->moduleData->getDemand();
        } else {
            $this->moduleData->setDemand($backendUserGroupDemand);
        }

        $compareBackendUserGroupList = $this->moduleData->getCompareGroupList();
        $this->view->assignMultiple(
            array(
                'backendUserGroupDemand' => $backendUserGroupDemand,
                'backendUserGroups' => $this->backendUserGroupRepository->findByDemand($backendUserGroupDemand),
                'compareBackendUserGroupList' => !empty($compareBackendUserGroupList) ? $this->backendUserGroupRepository->findByUidList($compareBackendUserGroupList) : '',
            )

        );
    }

    /**
     * Compare backend groups
     *
     * @return void
     */
    public function compareAction()
    {
        $compareBackendUserGroupList = $this->moduleData->getCompareGroupList();
        $this->view->assign('compareBackendUserGroupList',
            $this->backendUserGroupRepository->findByUidList($compareBackendUserGroupList));
    }

    /**
     * Attaches one backend user group to the compare list
     *
     * @param integer $uid
     * @retun void
     */
    public function addToCompareListAction($uid)
    {
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
    public function removeFromCompareListAction($uid)
    {
        $this->moduleData->detachUidCompareGroup($uid);
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
        return 'MyUserManagementMyusermanagement_MyUserManagementGroupadmin';
    }
}