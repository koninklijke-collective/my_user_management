<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\AbstractButton;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Hook: ButtonBar retrieval
 */
final class ButtonBarHook
{
    use BackendUserAuthenticationTrait;

    protected ServerRequest $request;

    public function __construct(?ServerRequest $request = null)
    {
        $this->request = $request ?? $GLOBALS['TYPO3_REQUEST'];
    }

    /**
     * Change button bar when editing BackendUser & BackendUserGroup
     */
    public function getButtons(array $parameters, ButtonBar $buttonBar): array
    {
        $buttons = $parameters['buttons'];

        // Admins always have access to all buttons
        if (self::getBackendUserAuthentication()->isAdmin()) {
            return $buttons;
        }

        if (!$this->isEditRecord()) {
            return $buttons;
        }

        $table = $this->getEditedTable();
        if (!in_array($table, [BackendUser::TABLE, BackendUserGroup::TABLE], true)) {
            return $buttons;
        }

        return $this->filterButtonsForCurrentUser($buttons, $table);
    }

    protected function filterButtonsForCurrentUser(array $buttons, string $currentTable): array
    {
        $result = [];
        foreach ($buttons as $position => $rows) {
            foreach ($rows as $key => $buttonGroup) {
                foreach ($buttonGroup as $index => $button) {
                    // Check rights for button by icon identifier
                    if ($button instanceof AbstractButton) {
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

    protected function isEditRecord(): bool
    {
        $route = $this->request->getQueryParams()['route'] ?? 'unknown';

        return $route === '/record/edit';
    }

    protected function getEditedTable(): ?string
    {
        $table = array_key_first($this->request->getQueryParams()['edit'] ?? []);
        if (!is_string($table) || empty($table)) {
            return null;
        }

        return $table;
    }
}
