<?php
namespace Serfhos\MyUserManagement\Domain\Model;

/**
 * Domain model for backend user group demands
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class BackendUserGroupDemand
{

    /**
     * The title
     *
     * @var string
     */
    protected $title;

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}