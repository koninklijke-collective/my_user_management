<?php
namespace Serfhos\MyUserManagement\Hook;

use Serfhos\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;

/**
 * Hook: TCA Manipulation
 *
 * @package Serfhos\MyUserManagement\Hook
 */
class TableConfigurationArrayHook implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Filter configured backend user groups based on Custom Options
     *
     * @param array $parameters
     * @param \TYPO3\CMS\Backend\Form\DataPreprocessor $reference
     * @return void
     */
    public function filterConfiguredBackendGroups($parameters, $reference)
    {
        // Only change items when inside form engine & non-admin
        if (($reference instanceof \TYPO3\CMS\Backend\Form\FormEngine) && $this->getBackendUserAuthentication()->isAdmin() === false) {
            $items = array();
            foreach ((array) $parameters['items'] as $item) {
                list(, $uid) = $item;
                if ($this->getBackendUserAuthentication()->check('custom_options', BackendUserGroupPermission::KEY . ':' . (int) $uid)) {
                    $items[] = $item;
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
