<?php
namespace Serfhos\MyUserManagement\Domain\Model\Dto;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 3 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * RestrictBackendUserGroupPermission
 */
class RestrictBackendUserGroupPermission implements \ArrayAccess
{
    const KEY = 'tx_myusermanagement_restrictbackendusergroup';

    protected static $locallang = 'EXT:my_user_management/Resources/Private/Language/locallang.xlf';
    protected $array = null;

    protected function initialize()
    {
        $this->array = [];
        $beGroups = $this->getDatabasConnection()->exec_SELECTgetRows('uid,title,description', 'be_groups', 'deleted=0 AND hidden=0', '', 'title');
        if (!empty($beGroups)) {
            $this->array['header'] = 'LLL:' . static::$locallang . ':restrictbackendusergroup.header';
            $this->array['items'] = [];
            foreach ($beGroups as $beGroup) {
                $this->array['items'][$beGroup['uid']] = [
                    $beGroup['title'],
                    'EXT:core/Resources/Public/Icons/T3Icons/status/status-user-group-backend.svg',
                    $beGroup['description'],
                ];
            }
        }
    }

    /**
     * @return TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabasConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    // {{{ ArrayAccess
    final public function offsetSet($offset, $value)
    {
        if ($this->array === null) {
            $this->initialize();
        }
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }
    final public function offsetExists($offset)
    {
        if ($this->array === null) {
            $this->initialize();
        }
        return isset($this->array[$offset]);
    }
    final public function offsetUnset($offset)
    {
        if ($this->array === null) {
            $this->initialize();
        }
        unset($this->array[$offset]);
    }
    final public function offsetGet($offset)
    {
        if ($this->array === null) {
            $this->initialize();
        }
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }
    // }}}
}
