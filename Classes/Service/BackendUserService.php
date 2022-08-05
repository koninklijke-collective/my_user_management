<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserRepository;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Service: Backend User - Repository functionality with access check
 */
final class BackendUserService implements SingletonInterface
{
    use BackendUserAuthenticationTrait;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserRepository */
    protected $backendUserRepository;

    public function __construct(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }

    /**
     * Find users which has access to given page id
     * Checks db mounts
     *
     * @param  int  $pageId
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser[]
     */
    public function findUsersWithPageAccess(int $pageId): array
    {
        $rootLine = BackendUtility::BEgetRootLine($pageId);
        $rootLineIds = [];
        foreach ($rootLine as $page) {
            $rootLineIds[] = (int)$page['uid'];
        }

        return $this->findAllowedUsersInRootLine($rootLineIds);
    }

    /**
     * Find all known backend users
     *
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser[]
     */
    public function findAllBackendUsers(): array
    {
        return $this->filterUsers($this->backendUserRepository->findAllActive());
    }

    /**
     * @return array
     */
    public function findAllBackendUsersForDropdown(): array
    {
        $users = [];
        foreach ($this->findAllBackendUsers() as $user) {
            $users[$user->getUid()] = $user->getUserName()
                . ($user->getRealName() ? ' (' . $user->getRealName() . ')' : '');
        }

        return $users;
    }

    /**
     * Find all inactive backend users
     *
     * @param  \DateTime  $since
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser[]
     * @throws \Exception
     */
    public function findAllInactiveBackendUsers(DateTime $since): array
    {
        return $this->filterUsers($this->backendUserRepository->findAllInactive($since), true);
    }

    /**
     * Find backend user
     *
     * @param  int  $userId
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser|null
     */
    public function findBackendUser(int $userId): ?BackendUser
    {
        $user = $this->backendUserRepository->findByUid($userId);
        if ($user === null) {
            return null;
        }

        if (!$this->isAllowedUser($user)) {
            return null;
        }

        return $user;
    }

    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array  $result
     * @param  bool  $displayDisabled
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser[]
     */
    protected function filterUsers($result, bool $displayDisabled = false): array
    {
        $users = [];
        foreach ($result as $user) {
            if (!$user instanceof BackendUser) {
                continue;
            }

            if (!$displayDisabled && $user->getIsDisabled()) {
                continue;
            }

            if (!$this->isAllowedUser($user)) {
                continue;
            }

            $users[] = $user;
        }

        return $users;
    }

    /**
     * Check if given user is allowed for current logged-in user
     *
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser  $user
     * @return bool
     */
    public function isAllowedUser(BackendUser $user): bool
    {
        if ($user->getIsAdministrator() && !self::getBackendUserAuthentication()->isAdmin()) {
            // Ignore admins if a non admin is retrieving the information!
            return false;
        }

        if (str_starts_with($user->getUserName(), '_cli_')) {
            return false;
        }

        return true;
    }

    /**
     * Find users that have access in rootline
     *
     * @param  array  $rootLine
     * @return \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser[]
     */
    protected function findAllowedUsersInRootLine(array $rootLine): array
    {
        $users = [];
        foreach ($this->findAllBackendUsers() as $user) {
            if ($user instanceof BackendUser) {
                if ($user->getIsAdministrator()) {
                    $users[] = $user;
                } elseif (count($match = array_intersect($rootLine, $user->getInheritedMountPoints())) > 0) {
                    $user->setActiveMountPoints($match);
                    $users[] = $user;
                }
            }
        }

        return $users;
    }
}
