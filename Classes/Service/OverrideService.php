<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\MenuRegistry;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Service: Override Default Functionality
 */
class OverrideService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @param \TYPO3\CMS\Backend\Template\Components\ButtonBar $buttonBar
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @param \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory
     * @return void
     */
    public function registerDocheaderButtons(ButtonBar $buttonBar, Request $request, IconFactory $iconFactory)
    {
        $moduleName = $request->getPluginName();
        $getVars = $request->getArguments();

        $extensionName = $request->getControllerExtensionName();
        if (count($getVars) === 0) {
            $modulePrefix = strtolower('tx_' . $extensionName . '_' . $moduleName);
            $getVars = ['id', 'M', $modulePrefix];
        }
        $shortcutName = $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUsers');
        if ($request->getControllerName() === 'BackendUser') {
            if ($request->getControllerActionName() === 'index' && AccessUtility::beUserHasRightToAddTable(BackendUser::TABLE)) {
                $returnUrl = rawurlencode(BackendUtility::getModuleUrl($moduleName));
                $parameters = GeneralUtility::explodeUrl2Array('edit[be_users][0]=new&returnUrl=' . $returnUrl);
                $addUserLink = BackendUtility::getModuleUrl('record_edit', $parameters);
                $title = $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newRecordGeneral');
                $icon = $iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL);
                $addUserButton = $buttonBar->makeLinkButton()
                    ->setHref($addUserLink)
                    ->setTitle($title)
                    ->setIcon($icon);
                $buttonBar->addButton($addUserButton, ButtonBar::BUTTON_POSITION_LEFT);
            }
            if ($request->getControllerActionName() === 'compare') {
                $addUserLink = BackendUtility::getModuleUrl($moduleName);
                $title = $this->getLanguageService()->sL('LLL:EXT:lang/locallang_core.xlf:labels.goBack');
                $icon = $iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL);
                $addUserButton = $buttonBar->makeLinkButton()
                    ->setHref($addUserLink)
                    ->setTitle($title)
                    ->setIcon($icon);
                $buttonBar->addButton($addUserButton, ButtonBar::BUTTON_POSITION_LEFT);
            }
            if ($request->getControllerActionName() === 'online') {
                $shortcutName = $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:onlineUsers');
            }
        }
        if ($request->getControllerName() === 'BackendUserGroup' && AccessUtility::beUserHasRightToAddTable(BackendUserGroup::TABLE)) {
            $shortcutName = $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUserGroupsMenu');
            $returnUrl = rawurlencode(BackendUtility::getModuleUrl($moduleName, [
                'tx_myusermanagement_myusermanagement_myusermanagementuseradmin' => [
                    'action' => 'index',
                    'controller' => 'BackendUserGroup',
                ],
            ]));
            $parameters = GeneralUtility::explodeUrl2Array('edit[be_groups][0]=new&returnUrl=' . $returnUrl);
            $addUserLink = BackendUtility::getModuleUrl('record_edit', $parameters);
            $title = $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newRecordGeneral');
            $icon = $iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL);
            $addUserGroupButton = $buttonBar->makeLinkButton()
                ->setHref($addUserLink)
                ->setTitle($title)
                ->setIcon($icon);
            $buttonBar->addButton($addUserGroupButton, ButtonBar::BUTTON_POSITION_LEFT);
        }
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName($moduleName)
            ->setDisplayName($shortcutName)
            ->setGetVariables($getVars);
        $buttonBar->addButton($shortcutButton);
    }

    /**
     * Generate menu for non-admin views
     *
     * @param \TYPO3\CMS\Backend\Template\Components\MenuRegistry $menuRegistry
     * @param \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @return void
     */
    public function generateMenu(MenuRegistry $menuRegistry, UriBuilder $uriBuilder, Request $request)
    {
        $menuItems = [];
        if (AccessUtility::beUserHasRightToSeeTable(BackendUser::TABLE)) {
            $menuItems['index'] = [
                'controller' => 'BackendUser',
                'action' => 'index',
                'label' => $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUsers'),
            ];
        }

        if (AccessUtility::beUserHasRightToSeeTable(BackendUserGroup::TABLE)) {
            $menuItems['pages'] = [
                'controller' => 'BackendUserGroup',
                'action' => 'index',
                'label' => $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUserGroupsMenu'),
            ];
        }

        if (!empty($menuItems)) {
            $uriBuilder->setRequest($request);

            $menu = $menuRegistry->makeMenu();
            $menu->setIdentifier('BackendUserModuleMenu');

            foreach ($menuItems as $menuItemConfig) {
                if ($request->getControllerName() === $menuItemConfig['controller']) {
                    $isActive = $request->getControllerActionName() === $menuItemConfig['action'] ? true : false;
                } else {
                    $isActive = false;
                }
                $menuItem = $menu->makeMenuItem()
                    ->setTitle($menuItemConfig['label'])
                    ->setHref($uriBuilder->reset()->uriFor($menuItemConfig['action'], [], $menuItemConfig['controller']))
                    ->setActive($isActive);
                $menu->addMenuItem($menuItem);
            }

            $menuRegistry->addMenu($menu);
        }
    }

    /**
     * Insert generic Javascript interaction
     * Make sure that the allowed actions are displayed
     *
     * @param string $table
     * @return void
     */
    public function insertJavascriptInteraction($table = BackendUser::TABLE)
    {
        $pageRenderer = $this->getPageRenderer();
        if ($pageRenderer instanceof \TYPO3\CMS\Core\Page\PageRenderer) {
            // Use same logic as PageRenderer
            $jsFile = GeneralUtility::getFileAbsFileName('EXT:my_user_management/Resources/Public/JavaScripts/jquery-backend.js');
            $jsFile = PathUtility::getRelativePath(PATH_typo3, $jsFile);
            $jsFile = rtrim($jsFile, '/');

            if ($noAccess = $this->getHiddenAccessOptions($table)) {
                $pageRenderer->addMetaTag('<meta property="extension-no-access" content="' . implode(',', $noAccess) . '" />');
            }

            $pageRenderer->addJsFile($jsFile);
        }
    }

    /**
     * Get non-configured access rights which should be disabled for core workflow
     *
     * @param string $table
     * @return array
     * @throws \Exception
     */
    protected function getHiddenAccessOptions($table)
    {
        $noAccess = [];
        if ($this->getBackendUserAuthentication()->isAdmin()) {
            return $noAccess;
        }

        if (AccessUtility::beUserHasRightToEditTable($table) === false) {
            $noAccess[] = 'action-edit';
        }

        if (
            AccessUtility::beUserHasRightToEditTableField($table, 'disable') === false
            && AccessUtility::beUserHasRightToEditTableField($table, 'hidden') === false
        ) {
            $noAccess[] = 'action-hide';
        }

        switch ($table) {
            case BackendUser::TABLE:
                if (BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_USER) === false) {
                    $noAccess[] = 'action-delete';
                }

                if (BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_SWITCH_USER) === false) {
                    $noAccess[] = 'action-switch-user';
                }
                break;
            case BackendUserGroup::TABLE:
                if (BackendUserActionPermission::isConfigured(BackendUserActionPermission::ACTION_DELETE_GROUP) === false) {
                    $noAccess[] = 'action-delete';
                }
                break;

            default:
                throw new \Exception('Given table not supported (' . $table . ')', 1479913799515);
        }

        return $noAccess;
    }

    /**
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected function getPageRenderer()
    {
        return $this->getObjectManager()->get(\TYPO3\CMS\Core\Page\PageRenderer::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
