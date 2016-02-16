<?php
namespace Serfhos\MyUserManagement\Domain\Repository;

/**
 * Repository: BackendUserGroup
 *
 * @package Serfhos\MyUserManagement\Domain\Repository
 */
class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{
    /**
     * @var Serfhos\MyUserManagement\Service\RestrictBackendUserGroupService
     * @inject
     */
    protected $restrictBackendUserGroupService;

    public function findAll()
    {
        $objects = parent::findAll();

        if ($this->getBackendUserAuthentication()->isAdmin() === false) {
            $objects = $this->restrictBackendUserGroupService->getRestrictedBackendUserGroups();
        }

        return $objects;
    }

    /**
     * Find records by uids
     *
     * @param  array  $uids
     * @return TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByUids(array $uids)
    {
        /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();

        $query->matching(
            $query->in('uid', $uids)
        );
        // $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
        // $a = $query->execute();
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($a);
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

        return $query->execute();
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
