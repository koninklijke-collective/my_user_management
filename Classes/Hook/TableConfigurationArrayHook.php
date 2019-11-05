<?php

namespace KoninklijkeCollective\MyUserManagement\Hook;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook: TCA Manipulation
 */
class TableConfigurationArrayHook implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Filter configured backend user groups based on Custom Options
     *
     * @param array $parameters
     * @return void
     */
    public function filterConfiguredBackendGroups($parameters)
    {
        // Only change items when non-admin
        if ($this->getBackendUserAuthentication()->isAdmin() === false && !empty($parameters['items'])) {
            $selectedItems = GeneralUtility::trimExplode(',', $parameters['row'][$parameters['field']], true);
            $items = [];
            foreach ((array) $parameters['items'] as $item) {
                [, $uid] = $item;
                // Is it configured for the user?
                if ($this->getBackendUserAuthentication()->check('custom_options', BackendUserGroupPermission::KEY . ':' . (int) $uid)) {
                    $items[] = $item;
                } elseif (in_array($uid, $selectedItems)) {
                    // If its one of the already selected and we don't have access. We still need to show it or the user
                    // can unconsciously remove the group.
                    // @TODO The user can still delete the group. Hook DataHandler should prevent this.
                    $items[] = [
                        '_hidden_ ' . $item[0],
                        $item[1],
                        $item[2],
                    ];
                }
            }

            // Only apply when there are any items filtered. Fallback on all items!
            if (!empty($items)) {
                $parameters['items'] = $items;
            }
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
