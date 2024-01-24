<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserGroupRepository as CustomBackendUserGroupRepository;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\BackendUserRepository as CustomBackendUserRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository;
use TYPO3\CMS\Beuser\Service\UserInformationService;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Override Controller: BackendUser
 *
 * @see \TYPO3\CMS\Beuser\Controller\BackendUserController
 */
final class BackendUserController extends \TYPO3\CMS\Beuser\Controller\BackendUserController
{
    use TranslateTrait;

    public function __construct(
        CustomBackendUserRepository $backendUserRepository,
        CustomBackendUserGroupRepository $backendUserGroupRepository,
        BackendUserSessionRepository $backendUserSessionRepository,
        UserInformationService $userInformationService,
        ModuleTemplateFactory $moduleTemplateFactory,
        BackendUriBuilder $backendUriBuilder,
        IconFactory $iconFactory,
        PageRenderer $pageRenderer
    ) {
        parent::__construct(
            $backendUserRepository,
            $backendUserGroupRepository,
            $backendUserSessionRepository,
            $userInformationService,
            $moduleTemplateFactory,
            $backendUriBuilder,
            $iconFactory,
            $pageRenderer
        );
    }

    public function indexAction(Demand $demand = null, int $currentPage = 1, string $operation = ''): ResponseInterface
    {
        if (!AccessUtility::beUserHasRightToEditTable(BackendUser::TABLE)) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUser::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        return parent::indexAction($demand, $currentPage, $operation);
    }

    /**
     * Displays all BackendUserGroups
     *
     * @param  int  $currentPage
     * @return ResponseInterface
     */
    public function groupsAction(int $currentPage = 1): ResponseInterface
    {
        if (!AccessUtility::beUserHasRightToEditTable(BackendUserGroup::TABLE)) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUserGroup::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        return parent::groupsAction($currentPage);
    }
}
