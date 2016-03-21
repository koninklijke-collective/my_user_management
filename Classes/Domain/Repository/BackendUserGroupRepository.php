<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * Repository: BackendUser
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{

    /**
     * @var string
     */
    const TABLE = 'be_groups';

    /**
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllowed()
    {
        $query = $this->createQuery();
        $allowed = $this->getAllowedGroups();
        // @TODO
        return $query->execute();
    }

    /**
     * Get secured allowed groups
     *
     * @return array
     */
    protected function getAllowedGroups()
    {
        $allowed = array();
        $options = $this->getBackendUserAuthentication()->groupData['custom_options'];
        foreach (GeneralUtility::trimExplode(',', $options, true) as $optionValue) {
            if (strpos($optionValue, RestrictBackendUserGroupPermission::KEY) === 0) {
                $uids[] = (int) substr($optionValue, strlen(RestrictBackendUserGroupPermission::KEY) + 1);
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
