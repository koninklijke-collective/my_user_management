<?php
namespace Serfhos\MyUserManagement\Hooks;

use Serfhos\MyUserManagement\Domain\Model\BackendUser;
use Serfhos\MyUserManagement\Domain\Model\BackendUserGroup;
use Serfhos\MyUserManagement\Service\RestrictBackendUserGroupService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * ItemsProcFunc
 *
 * @package Serfhos\MyUserManagement\Hooks
 */
class ItemsProcFunc implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var Serfhos\MyUserManagement\Service\RestrictBackendUserGroupService
     */
    protected $restrictBackendUserGroupService;

    public function __construct()
    {
        $this->restrictBackendUserGroupService = GeneralUtility::makeInstance(ObjectManager::class)->get(RestrictBackendUserGroupService::class);
    }

    /**
     * Overwrite items if any group is choosen in custom permissions.
     *
     * @param  array                                                   &$params [description]
     * @param  \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems &$pObj   [description]
     * @return void
     */
    public function getRestrictedBackendUserGroups(array &$params, \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems &$pObj)
    {
        $groups = $this->restrictBackendUserGroupService->getRestrictedBackendUserGroups();
        if (count($groups)) {
            $items = [];
            foreach ($groups as $group) {
                $items[] = [
                    $group->getTitle(),
                    $group->getUid(),
                    'status-user-group-backend',
                ];
            }
            $params['items'] = $items;
        }
    }
}
