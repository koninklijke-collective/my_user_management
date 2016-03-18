<?php
namespace Serfhos\MyUserManagement\Domain\DataTransferObject;

/**
 * DTO: Permission access Backend User Groups
 *
 * @package Serfhos\MyUserManagement\Domain\Model\DataTransferObject
 */
class BackendUserGroupPermission implements \ArrayAccess
{

    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor: BackendUserGroupPermission
     */
    public function __construct()
    {
        $this->initializeData();
    }

    /**
     * @return array
     */
    protected function initializeData()
    {
        if ($this->data === null) {
            $this->data = array(
                'header' => '',
                'items' => array(),
            );
            $groups = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordsByField('be_groups', 'hide_in_lists', 0);
            $icon = \TYPO3\CMS\Backend\Utility\IconUtility::getIcon('be_groups');
            foreach ((array) $groups as $group) {
                $this->data['items'][$group['uid']] = array(
                    $group['title'],
                    $icon,
                    $group['description'],
                );
            }
        }
        return $this->data;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}