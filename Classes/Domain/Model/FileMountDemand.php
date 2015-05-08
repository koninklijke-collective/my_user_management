<?php
namespace Serfhos\MyUserManagement\Domain\Model;

/**
 * Domain for file mount demands
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * The title
     *
     * @var string
     */
    protected $title;

    /**
     * The path
     *
     * @var string
     */
    protected $path;

    /**
     * Sets the Path
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Gets the Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the Title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}