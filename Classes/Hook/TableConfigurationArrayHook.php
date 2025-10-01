<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\QueryBuilderTrait;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook: TCA Manipulation
 */
final class TableConfigurationArrayHook implements SingletonInterface
{
    use QueryBuilderTrait;
    use BackendUserAuthenticationTrait;

    /**
     * ItemsProcFunc: Filter configured backend user groups based on Custom Options
     *
     * @see https://forge.typo3.org/issues/85622
     */
    public function filterConfiguredBackendGroups(array &$parameters): void
    {
        if (!isset($parameters['items'])) {
            $parameters['items'] = [];
        }

        if (self::getBackendUserAuthentication()->isAdmin()) {
            return;
        }

        $selectedItems = GeneralUtility::intExplode(',', $parameters['row'][$parameters['field']], true);
        $options = [];
        foreach ($parameters['items'] as $item) {
            // Get id from option
            [$label, $id, $icon] = $item;
            $id = (int)$id;
            // Add to $options when group if user
            $options = $this->addGroupBasedOnUserAccess($options, $selectedItems, $id, $label, $icon);
        }

        // Only apply when there are any items filtered. Fallback on default items!
        if (!empty($options)) {
            $parameters['items'] = $options;
        }
    }

    protected function addGroupBasedOnUserAccess(
        array $items,
        array $selected,
        int $id,
        string $label,
        ?string $icon = null
    ): array {
        // If user has access, just add to items and return as intended
        if (BackendUserGroupPermission::hasAccessToGroup($id)) {
            $items[] = [$label, $id, $icon];

            return $items;
        }

        // If its one of the already selected and we don't have access. We still need to show it or the user
        // can unconsciously remove the group.
        // @TODO The user can still delete the group. Hook DataHandler should prevent this.
        if (in_array($id, $selected, true)) {
            $items[] = ['_hidden_ ' . $label, $id, $icon];

            return $items;
        }

        // If none, just return the already parsed items
        return $items;
    }
}
