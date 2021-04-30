<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository as CoreBackendUserRepository;
use TYPO3\CMS\Beuser\Service\UserInformationService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/**
 * Controller: BackendUserGroup
 */
final class BackendUserGroupController extends \TYPO3\CMS\Beuser\Controller\BackendUserGroupController
{
    use TranslateTrait;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository */
    protected $backendUserGroupRepository;

    public function __construct(
        UserInformationService $userInformationService,
        BackendUserGroupRepository $backendUserGroupRepository
    ) {
        parent::__construct($userInformationService);
        $this->backendUserGroupRepository = $backendUserGroupRepository;
    }

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository  $backendUserGroupRepository
     */
    public function injectBackendUserGroupRepository(CoreBackendUserRepository $backendUserGroupRepository): void
    {
        // @see self::__construct(); Dont inject through this.. ignore parent inject
    }

    /**
     * Displays all BackendUserGroups
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function indexAction(): void
    {
        if (AccessUtility::beUserHasRightToEditTable(BackendUserGroup::TABLE) === false) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUserGroup::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        $this->view->assignMultiple([
            'shortcutLabel' => 'MyBackendUserGroupsMenu',
            'backendUserGroups' => $this->backendUserGroupRepository->findAllConfigured(),
        ]);
    }
}
