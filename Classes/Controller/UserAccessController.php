<?php
namespace Serfhos\MyUserManagement\Controller;

/**
 * User management controller
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class UserAccessController extends AbstractBackendController
{

    /**
     * @var \Serfhos\MyUserManagement\Service\AccessService
     * @inject
     */
    protected $accessService;

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

        $backendUsers = $this->accessService->findUsersWithPageAccess($this->pageId, $demand);
        $this->view->assignMultiple(array(
            'backendUsers' => $backendUsers,
            'demand' => $demand
        ));
    }

    /**
     * Returns generic module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return 'MyUserManagementMyusermanagement_MyUserManagementUseraccess';
    }
}