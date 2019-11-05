<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/**
 * Controller: BackendUserGroup
 */
final class BackendUserGroupController extends \TYPO3\CMS\Beuser\Controller\BackendUserGroupController
{
    use TranslateTrait;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $backendUserGroupRepository;

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
                static::translate('backend_user_no_rights_to_table_description', [BackendUserGroup::TABLE]),
                static::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        $this->view->assignMultiple([
            'shortcutLabel' => 'MyBackendUserGroupsMenu',
            'backendUserGroups' => $this->backendUserGroupRepository->findAllConfigured(),
        ]);
    }
}
