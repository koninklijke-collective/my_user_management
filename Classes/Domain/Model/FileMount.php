<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Domain model for file mounts
 */
final class FileMount extends AbstractEntity
{
    public const TABLE = 'sys_filemounts';

    protected ?string $title;
    protected ?string $path;
    protected ?int $storage;
    protected ?bool $isDisabled;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $value): self
    {
        $this->path = $value;

        return $this;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setStorage(?int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setIsDisabled($isDisabled): self
    {
        $this->isDisabled = filter_var($isDisabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $this;
    }
}
