<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Controller\EditDocumentController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\AbstractButton;

/**
 * Hook: ButtonBar retrieval
 */
final class ButtonBarHook
{
    use BackendUserAuthenticationTrait;

    /**
     * Change button bar when editing BackendUser & BackendUserGroup
     *
     * @param  array  $parameters
     * @param  \TYPO3\CMS\Backend\Template\Components\ButtonBar  $buttonBar
     * @return array
     */
    public function getButtons(array $parameters, ButtonBar $buttonBar): array
    {
        $buttons = $parameters['buttons'];

        // Admins always have access to all buttons
        if (static::getBackendUserAuthentication()->isAdmin()) {
            return $buttons;
        }

        if (!$this->isEditRecord()) {
            return $buttons;
        }

        $table = $this->getEditedTable();
        if ($table && !in_array($this->getEditedTable(), [BackendUser::TABLE, BackendUserGroup::TABLE], true)) {
            return $buttons;
        }

        return $this->filterButtonsForCurrentUser($buttons, $table);
    }

    /**
     * @param  array  $buttons
     * @param  string  $currentTable
     * @return array
     * @throws \TYPO3\CMS\Extbase\Security\Exception
     */
    protected function filterButtonsForCurrentUser(array $buttons, string $currentTable): array
    {
        $result = [];
        foreach ($buttons as $position => $rows) {
            foreach ($rows as $key => $buttonGroup) {
                foreach ($buttonGroup as $index => $button) {
                    // Check rights for button by icon identifier
                    if ($button instanceof AbstractButton && $button->getIcon()) {
                        switch ($button->getIcon()->getIdentifier()) {
                            case 'actions-document-history-open':
                                continue 2;

                            case 'actions-edit-delete':
                                if (!AccessUtility::beUserHasRightToDeleteTable($currentTable)) {
                                    continue 2;
                                }
                                break;

                            case 'actions-add':
                                if (!AccessUtility::beUserHasRightToAddTable($currentTable)) {
                                    continue 2;
                                }
                                break;
                        }
                    }

                    // Add button if allowed
                    $result[$position][$key][$index] = $button;
                }
            }
        }

        return $result;
    }

    /**
     * @return boolean
     */
    protected function isEditRecord(): bool
    {
        return ($GLOBALS['SOBE'] instanceof EditDocumentController);
    }

    /**
     * @return string|null
     */
    protected function getEditedTable(): ?string
    {
        return $GLOBALS['SOBE']->firstEl['table'] ?? null;
    }
}
