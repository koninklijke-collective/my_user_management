<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Domain model for file mounts
 */
final class FileMount extends AbstractEntity
{
    public const TABLE = 'sys_filemounts';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $path = '';

    /** @var integer */
    protected $storage;

    /** @var boolean */
    protected $isDisabled;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param  string|null  $value
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount
     */
    public function setTitle(?string $value): FileMount
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Getter for the path of the file mount.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Setter for the path of the file mount.
     *
     * @param  string|null  $value
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount
     */
    public function setPath(?string $value): FileMount
    {
        $this->path = $value;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStorage(): ?int
    {
        return $this->storage;
    }

    /**
     * @param  int|null  $storage
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount
     */
    public function setStorage(?int $storage): FileMount
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    /**
     * Sets the disabled state
     *
     * @param  boolean|string  $isDisabled
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount
     */
    public function setIsDisabled($isDisabled): FileMount
    {
        $this->isDisabled = filter_var($isDisabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $this;
    }
}
