<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Domain model for file mounts
 *
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMount extends AbstractEntity
{

    /**
     * @var string
     */
    public const TABLE = 'sys_filemounts';

    /**
     * Title of the file mount.
     *
     * @var string
     * @validate notEmpty
     */
    protected $title = '';

    /**
     * Path of the file mount.
     *
     * @var string
     * @validate notEmpty
     */
    protected $path = '';

    /** @var integer */
    protected $storage;

    /**
     * Disabled record?
     *
     * @var boolean
     */
    protected $isDisabled;

    /**
     * Getter for the title of the file mount.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Setter for the title of the file mount.
     *
     * @param  string  $value
     * @return void
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Getter for the path of the file mount.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for the path of the file mount.
     *
     * @param  string  $value
     * @return void
     */
    public function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * Returns the Storage
     *
     * @return int
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Sets the Storage
     *
     * @param  int  $storage
     * @return void
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * Gets the disabled state
     *
     * @return boolean
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Sets the disabled state
     *
     * @param  boolean  $isDisabled
     * @return void
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }
}
