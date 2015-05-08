<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * User management controller
 *
 * @package Serfhos\MyUserManagement\Controller
 */
abstract class AbstractBackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \TYPO3\CMS\Beuser\Domain\Model\ModuleData
     */
    protected $moduleData;

    /**
     * @var \Serfhos\MyUserManagement\Service\ModuleDataStorageService
     * @inject
     */
    protected $moduleDataStorageService;

    /**
     * The action menu items
     *
     * @var array
     */
    protected $actionMenuItems = array();

    /**
     * Current Page ID from getter
     *
     * @var integer
     */
    protected $pageId;

    /**
     * Returns generic module name
     *
     * @return string
     */
    abstract protected function getModuleName();

    /**
     * Initialize the action menu items
     *
     * @return void
     */
    protected function initializeActionMenu()
    {
        $this->actionMenuItems = array();
    }

    /**
     * Load and persist module data
     *
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function processRequest(
        \TYPO3\CMS\Extbase\Mvc\RequestInterface $request,
        \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
    ) {
        $this->moduleData = $this->moduleDataStorageService->loadModuleData($this->getModuleName());

        // We "finally" persist the module data.
        try {
            parent::processRequest($request, $response);
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
        } catch (\TYPO3\CMS\Extbase\Mvc\Exception\StopActionException $e) {
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
            throw $e;
        }
    }

    /**
     * Initialize actions
     *
     * @return void
     * @throws \RuntimeException
     */
    public function initializeAction()
    {
        // @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
        // and settings. Thus, we need a TypoScript root template, that then loads the
        // ext_typoscript_setup.txt file of this module. This is nasty, but can not be
        // circumvented until there is a better solution in extbase.
        // For now we throw an exception if no settings are detected.
        if (empty($this->settings)) {
            throw new \RuntimeException('No settings detected. This module can not work then. This usually happens if there is no frontend TypoScript template with root flag set. '
                . 'Please create a frontend page with a TypoScript root template.',
                1344375003);
        }

        $this->pageId = (int) GeneralUtility::_GP('id');
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $this->initializeActionMenu();

        $view->assignMultiple(array(
            'actionMenuItems' => $this->actionMenuItems,
            'currentUser' => $this->getBackendUserAuthentication(),
            'returnUrl' => urlencode(BackendUtility::getModuleUrl($this->getModuleName())),
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'pageId' => $this->pageId,
        ));
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}