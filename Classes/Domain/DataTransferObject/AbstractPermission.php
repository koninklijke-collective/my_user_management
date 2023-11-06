<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use ArrayAccess;
use Countable;
use ReturnTypeWillChange;

/**
 * DTO: Permission access Backend User Groups
 *
 * @usage $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][class::KEY]
 * @see \TYPO3\CMS\Backend\Form\FormDataProvider\AbstractItemProvider::addItemsFromSpecial
 */
abstract class AbstractPermission implements ArrayAccess, Countable
{
    protected ?array $data;

    abstract protected function populateData(): void;

    public function __construct()
    {
        $this->populateData();
    }

    /**
     * Whether an offset exists
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     */
    public function count(): int
    {
        return count($this->data);
    }
}
