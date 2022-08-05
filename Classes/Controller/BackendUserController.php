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
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository;
use TYPO3\CMS\Beuser\Service\UserInformationService;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
        $backendUser = $this->getBackendUser();
        if ($operation === 'reset-filters') {
            // Reset the module data demand object
            $this->moduleData->setDemand(new Demand());
            $demand = null;
        }

        if ($demand === null) {
            $demand = $this->moduleData->getDemand();
        } else {
            if (!$backendUser->isAdmin()) {
                if ($demand->getUserType() !== Demand::USERTYPE_USERONLY) {
                    $this->addFlashMessage(
                        self::translate('filter_on_admin_is_not_allowed_description'),
                        self::translate('filter_on_admin_is_not_allowed_title'),
                        AbstractMessage::ERROR
                    );
                }
                $demand->setUserType(Demand::USERTYPE_USERONLY);
            }
            $this->moduleData->setDemand($demand);
        }

        $backendUser->pushModuleData('tx_beuser', $this->moduleData->forUc());

        $compareUserList = $this->moduleData->getCompareUserList();
        $backendUsers = $this->backendUserRepository->findDemanded($demand);
        $paginator = new QueryResultPaginator($backendUsers, $currentPage, 50);
        $pagination = new SimplePagination($paginator);

        $this->view->assignMultiple([
            'onlineBackendUsers' => $this->getOnlineBackendUsers(),
            'demand' => $demand,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'totalAmountOfBackendUsers' => $backendUsers->count(),
            'backendUserGroups' => array_merge([''], $this->backendUserGroupRepository->findAllConfigured()
                ->toArray()),
            'compareUserUidList' => array_combine($compareUserList, $compareUserList),
            'currentUserUid' => $backendUser->user['uid'],
            'compareUserList' => !empty($compareUserList) ? $this->backendUserRepository->findByUidList($compareUserList) : '',
        ]);

        $this->addMainMenu('index');
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $addUserButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL))
            ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newRecordGeneral'))
            ->setHref(
                $this->backendUriBuilder->buildUriFromRoute('record_edit', [
                    'edit' => ['be_users' => [0 => 'new']],
                    'returnUrl' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                ])
            );
        $buttonBar->addButton($addUserButton);
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementUseradmin')
            ->setDisplayName(LocalizationUtility::translate('myUserManagement', 'myUserManagement'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/SwitchUser');

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
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
        /** @var QueryResultInterface $groups */
        $groups = $this->backendUserGroupRepository->findAllConfigured();
        $paginator = new QueryResultPaginator($groups, $currentPage, 50);
        $pagination = new SimplePagination($paginator);
        $compareGroupUidList = array_keys($this->getBackendUser()->uc['beuser']['compareGroupUidList'] ?? []);
        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'totalAmountOfBackendUserGroups' => $groups->count(),
            'compareGroupUidList' => array_map(static function ($value) { // uid as key and force value to 1
                return 1;
            }, array_flip($compareGroupUidList)),
            'compareGroupList' => !empty($compareGroupUidList) ? $this->backendUserGroupRepository->findByUidList($compareGroupUidList) : [],
        ]);

        $this->addMainMenu('groups');
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $addGroupButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL))
            ->setTitle(LocalizationUtility::translate('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newRecordGeneral'))
            ->setHref(
                $this->backendUriBuilder->buildUriFromRoute('record_edit', [
                    'edit' => ['be_groups' => [0 => 'new']],
                    'returnUrl' => $this->request->getAttribute('normalizedParams')->getRequestUri(),
                ])
            );
        $buttonBar->addButton($addGroupButton);
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('myusermanagement_MyUserManagementUseradmin')
            ->setArguments(['tx_myusermanagement_myusermanagement_myusermanagementuseradmin' => ['action' => 'groups']])
            ->setDisplayName(LocalizationUtility::translate('myUserManagementGroups', 'myUserManagement'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }
}
