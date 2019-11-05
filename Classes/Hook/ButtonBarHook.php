<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;

/**
 * Hook: ButtonBar retrieval
 */
class ButtonBarHook
{

    /**
     * Change button bar when editing BackendUser & BackendUserGroup
     *
     * @param array $parameters
     * @param \TYPO3\CMS\Backend\Template\Components\ButtonBar $buttonBar
     * @return array
     */
    public function getButtons($parameters, ButtonBar $buttonBar)
    {
        if ($this->isEditRecord() && $this->isAdmin() === false) {
            $table = $this->getEditedTable();
            if ($table && in_array($table, [BackendUser::TABLE, BackendUserGroup::TABLE])) {
                foreach ($parameters['buttons'][ButtonBar::BUTTON_POSITION_LEFT] as $key => $buttonGroup) {
                    /** @var \TYPO3\CMS\Backend\Template\Components\Buttons\AbstractButton $button */
                    foreach ($buttonGroup as $index => $button) {
                        // Remove history button when editing be_users
                        if ($button->getIcon() && $button->getIcon()->getIdentifier() === 'actions-document-history-open') {
                            unset($parameters['buttons'][ButtonBar::BUTTON_POSITION_LEFT][$key][$index]);
                        }

                        // Remove delete button if user don't have access
                        if ($button->getIcon() && $button->getIcon()->getIdentifier() === 'actions-edit-delete') {
                            if (AccessUtility::beUserHasRightToDeleteTable($table) === false) {
                                unset($parameters['buttons'][ButtonBar::BUTTON_POSITION_LEFT][$key][$index]);
                            }
                        }
                    }
                }
            }
        }

        return $parameters['buttons'];
    }

    /**
     * @return boolean
     */
    protected function isAdmin()
    {
        /** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser */
        $backendUser = $GLOBALS['BE_USER'];
        return $backendUser->isAdmin();
    }

    /**
     * @return boolean
     */
    protected function isEditRecord()
    {
        return ($GLOBALS['SOBE'] instanceof \TYPO3\CMS\Backend\Controller\EditDocumentController);
    }

    /**
     * @return string
     */
    protected function getEditedTable()
    {
        return $GLOBALS['SOBE']->firstEl['table'];
    }
}
