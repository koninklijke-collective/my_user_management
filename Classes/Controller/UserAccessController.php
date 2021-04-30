<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Service\BackendUserService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Controller: User Access
 */
final class UserAccessController extends ActionController
{
    use TranslateTrait;
    use BackendUserAuthenticationTrait;

    /**
     * @var \KoninklijkeCollective\MyUserManagement\Service\BackendUserService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $backendUserService;

    /**
     * @param  \TYPO3\CMS\Extbase\Mvc\View\ViewInterface  $view
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        $this->view->assignMultiple([
            'shortcutLabel' => 'MyUserAccess',
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    /**
     * Action: List users
     *
     * @return void
     */
    public function indexAction(): void
    {
        $page = $this->getSelectedPage();
        if ($page === null) {
            $this->addFlashMessage(
                self::translate('no_page_selected_description'),
                self::translate('no_page_selected_title'),
                AbstractMessage::WARNING
            );

            return;
        }
        $this->view->assignMultiple([
            'page' => $page,
            'backendUsers' => $this->getBackendUserService()->findUsersWithPageAccess($page['uid']),
        ]);
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\BackendUserService
     */
    protected function getBackendUserService(): BackendUserService
    {
        if ($this->backendUserService === null) {
            $this->backendUserService = $this->objectManager->get(BackendUserService::class);
        }

        return $this->backendUserService;
    }

    /**
     * @return array|null
     */
    protected function getSelectedPage(): ?array
    {
        // @todo refactor for generic ServerRequest instead of using the internal Factory
        $request = ServerRequestFactory::fromGlobals();
        $pageId = (int)($request->getParsedBody()['id'] ?? $request->getQueryParams()['id'] ?? 0);
        if ($pageId === 0) {
            return null;
        }

        $page = BackendUtility::readPageAccess(
            $pageId,
            self::getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW)
        );

        return is_array($page) ? $page : null;
    }
}
