<?php
namespace Serfhos\MyUserManagement\Domain\Model;

/***************************************************************
 * Copyright notice
 *
 * (c) 2013 Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Domain model for file mounts
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMount extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

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

    /**
     * @var integer
     */
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
    public function getTitle() {
        return $this->title;
    }

    /**
     * Setter for the title of the file mount.
     *
     * @param string $value
     * @return void
     */
    public function setTitle($value) {
        $this->title = $value;
    }

    /**
     * Getter for the path of the file mount.
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Setter for the path of the file mount.
     *
     * @param string $value
     * @return void
     */
    public function setPath($value) {
        $this->path = $value;
    }

    /**
     * Sets the Storage
     *
     * @param int $storage
     * @return void
     */
    public function setStorage($storage) {
        $this->storage = $storage;
    }

    /**
     * Returns the Storage
     *
     * @return int
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * Sets the disabled state
     *
     * @param boolean $isDisabled
     * @return void
     */
    public function setIsDisabled($isDisabled) {
        $this->isDisabled = $isDisabled;
    }

    /**
     * Gets the disabled state
     *
     * @return boolean
     */
    public function getIsDisabled() {
        return $this->isDisabled;
    }

}