<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\DataHandling\DataHandlerCheckModifyAccessListHookInterface;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Security\Exception;

/**
 * Hook: DataHandler Manipulation when adjusting records for current backend user
 */
final class DataHandlerCheckModifyAccessListHook implements DataHandlerCheckModifyAccessListHookInterface
{

    /**
     * Hook that determines whether a user has access to modify a table.
     *
     * @inheritDoc
     * @param  bool &$accessAllowed  Whether the user has access to modify a table
     * @param  string  $table  The name of the table to be modified
     * @param  \TYPO3\CMS\Core\DataHandling\DataHandler  $parent  The calling parent object
     * @throws \TYPO3\CMS\Extbase\Security\Exception
     */
    public function checkModifyAccessList(&$accessAllowed, $table, DataHandler $parent)
    {
        // if already false processed, don't do anything..
        if (!$accessAllowed) {
            return;
        }

        // Only apply on this extensions table to minimize collision with other extension hooks
        if (!in_array($table, [BackendUser::TABLE, BackendUserGroup::TABLE, FileMount::TABLE], true)) {
            return;
        }

        // If user is not allowed to edit this table through configuration
        if (!AccessUtility::beUserHasRightToEditTable($table)) {
            $accessAllowed = false;

            return;
        }

        // Check rights per invoked action
        if (isset($parent->cmdmap[$table]) && is_array($parent->cmdmap)) {
            foreach ($parent->cmdmap[$table] as $id => $incomingCmdArray) {
                foreach ($incomingCmdArray as $command => $value) {
                    switch ($command) {
                        case 'delete':
                            $accessAllowed = $this->accessAllowedOnAction($table, 'delete');
                            break;
                        case 'undelete':
                            $accessAllowed = $this->accessAllowedOnAction($table, 'insert');
                            break;
                    }
                }
            }
        }

        if (isset($parent->datamap[$table]) && is_array($parent->datamap)) {
            foreach ($parent->datamap[$table] as $id => $value) {
                if (strpos($id, 'NEW') !== false || MathUtility::canBeInterpretedAsInteger($id) === false) {
                    $accessAllowed = $this->accessAllowedOnAction($table, 'insert');
                } else {
                    $accessAllowed = $this->accessAllowedOnAction($table, 'update');
                }
            }
        }
    }

    /**
     * @param  string  $table
     * @param  string  $action
     * @return bool true, or exception otherwise
     * @throws \TYPO3\CMS\Extbase\Security\Exception
     */
    protected function accessAllowedOnAction(string $table, string $action): bool
    {
        // Only access check for insert/delete/update possible by this extension configuration!
        switch ($action) {
            case 'insert':
                if (!AccessUtility::beUserHasRightToAddTable($table)) {
                    throw new Exception('You are not allowed to add new (' . $table . ') records!');
                }
                break;

            case 'delete':
                if (!AccessUtility::beUserHasRightToDeleteTable($table)) {
                    throw new Exception('You are not allowed to delete (' . $table . ') records!');
                }
                break;

            case 'update':
                if (!AccessUtility::beUserHasRightToEditTable($table)) {
                    throw new Exception('You are not allowed to update (' . $table . ') records!');
                }
                break;
        }

        return true;
    }
}
