<?php
namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Core\DataHandling\DataHandlerCheckModifyAccessListHookInterface;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Security\Exception;

/**
 * Hook: TCA Manipulation
 *
 * @package KoninklijkeCollective\MyUserManagement\Hook
 */
class DataHandlerCheckModifyAccessListHook implements DataHandlerCheckModifyAccessListHookInterface
{

    /**
     * Hook that determines whether a user has access to modify a table.
     *
     * @param bool &$accessAllowed Whether the user has access to modify a table
     * @param string $table The name of the table to be modified
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parent The calling parent object
     * @throws Exception
     */
    public function checkModifyAccessList(&$accessAllowed, $table, \TYPO3\CMS\Core\DataHandling\DataHandler $parent)
    {
        if (in_array($table, [BackendUser::TABLE, BackendUserGroup::TABLE]) && $accessAllowed === true) {
            $action = 'unknown';
            if (isset($parent->cmdmap[$table])) {
                foreach ($parent->cmdmap[$table] as $id => $incomingCmdArray) {
                    foreach ($incomingCmdArray as $command => $value) {
                        switch ($command) {
                            case 'delete':
                                $action = 'delete';
                                break;
                            case 'undelete':
                                $action = 'insert';
                                break;
                        }
                    }
                }
            } elseif (isset($parent->datamap[$table])) {
                foreach ($parent->datamap[$table] as $id => $value) {
                    if (strpos($id, 'NEW') !== false || MathUtility::canBeInterpretedAsInteger($id) === false) {
                        $action = 'insert';
                    } else {
                        $action = 'update';
                    }
                }
            }

            // Do access check for insert/delete!
            switch ($action) {
                case 'insert':
                    if (AccessUtility::beUserHasRightToAddTable($table) === false) {
                        $accessAllowed = false;
                        throw new Exception('You are not allowed to add new (' . $table . ') records!');
                    }
                    break;
                case 'delete':
                    if (AccessUtility::beUserHasRightToDeleteTable($table) === false) {
                        $accessAllowed = false;
                        throw new Exception('You are not allowed to delete (' . $table . ') records!');
                    }
                    break;
            }
        }
    }
}
