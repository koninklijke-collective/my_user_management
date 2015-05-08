<?php
namespace Serfhos\MyUserManagement\ViewHelpers\Widget\Controller;

/**
 * Groups for file mount controller
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class GroupsForFileMountController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{

    /**
     * The backend user group repository
     *
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * Displays all users from the group
     *
     * @return void
     */
    public function indexAction()
    {
        /* @var $fileMount \Serfhos\MyUserManagement\Domain\Model\FileMount */
        $fileMount = $this->widgetConfiguration['fileMount'];

        /* @var $demand \TYPO3\CMS\Beuser\Domain\Model\Demand */
        $backendUserGroups = $this->backendUserGroupRepository->findByFileMount($fileMount);

        $this->view->assign('backendUserGroups', $backendUserGroups);
    }
}
