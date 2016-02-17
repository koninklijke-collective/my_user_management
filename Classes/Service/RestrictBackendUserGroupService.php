<?php
namespace Serfhos\MyUserManagement\Service;

use Serfhos\MyUserManagement\Domain\Model\BackendUser;
use Serfhos\MyUserManagement\Domain\Model\BackendUserGroup;
use Serfhos\MyUserManagement\Domain\Model\Dto\RestrictBackendUserGroupPermission;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service: Permission
 *
 * @package Serfhos\MyUserManagement\Service
 */
class RestrictBackendUserGroupService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * Return groups selected in custom permissons.
     *
     * @return TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getRestrictedBackendUserGroups()
    {
        $uids = [];

        /** @var string $customPermissions */
        $customPermissions = $this->getBackendUserAuthentication()->groupData['custom_options'];

        // Read custom permissions and add selected groups to the storage.
        foreach (GeneralUtility::trimExplode(',', $customPermissions, true) as $optionValue) {
            if (strpos($optionValue, RestrictBackendUserGroupPermission::KEY) === 0) {
                $uids[] = (int) substr($optionValue, strlen(RestrictBackendUserGroupPermission::KEY) + 1);
            }
        }
        if (empty($uids)) {
            return $this->backendUserGroupRepository->findAll();
        }
        return $this->backendUserGroupRepository->findByUids($uids);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
