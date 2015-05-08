<?php
namespace Serfhos\MyUserManagement\ViewHelpers\Widget;

/**
 * Groups for file mount view helper
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class GroupsForFileMountWidgetViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper
{

    /**
     * @var \Serfhos\MyUserManagement\ViewHelpers\Widget\Controller\GroupsForFileMountController
     * @inject
     */
    protected $controller;

    /**
     * Render
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount
     * @return string
     */
    public function render(\Serfhos\MyUserManagement\Domain\Model\FileMount $fileMount)
    {
        return $this->initiateSubRequest();
    }
}
