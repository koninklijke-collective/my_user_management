<?php
namespace Serfhos\MyUserManagement\ViewHelpers\Widget;

/**
 * Renders a list of users from the specified group
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class UsersFromGroupWidgetViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper
{

    /**
     * @var \Serfhos\MyUserManagement\ViewHelpers\Widget\Controller\UsersFromGroupController
     * @inject
     */
    protected $controller;

    /**
     * Render
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\BackendUserGroup $backendUserGroup
     * @return string
     */
    public function render(\Serfhos\MyUserManagement\Domain\Model\BackendUserGroup $backendUserGroup)
    {
        return $this->initiateSubRequest();
    }
}
