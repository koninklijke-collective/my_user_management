<?php
namespace Serfhos\MyUserManagement\ViewHelpers\Widget\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * User from group controller
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class UsersFromGroupController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{

    /**
     * The backend user repository
     *
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;

    /**
     * Displays all users from the group
     *
     * @return void
     */
    public function indexAction()
    {
        /* @var $backendUserGroup \Serfhos\MyUserManagement\Domain\Model\BackendUserGroup */
        $backendUserGroup = $this->widgetConfiguration['backendUserGroup'];

        /* @var $demand \TYPO3\CMS\Beuser\Domain\Model\Demand */
        $demand = GeneralUtility::makeInstance('TYPO3\\CMS\\Beuser\\Domain\\Model\\Demand');
        $demand->setBackendUserGroup($backendUserGroup);
        $backendUsers = $this->backendUserRepository->findDemanded($demand);

        $this->view->assign('backendUsers', $backendUsers);
    }
}
