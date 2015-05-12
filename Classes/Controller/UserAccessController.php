<?php
namespace Serfhos\MyUserManagement\Controller;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: UserAccess
 *
 * @package Serfhos\MyUserManagement\Controller
 */
class UserAccessController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Serfhos\MyUserManagement\Service\AccessService
     * @inject
     */
    protected $accessService;

    /**
     * Action: List users
     *
     * @return void
     */
    public function indexAction()
    {
        $pageId = (int) GeneralUtility::_GP('id');
        $backendUsers = $this->accessService->findUsersWithPageAccess($pageId);

        if ($pageId === 0) {
            $this->addFlashMessage(
                $this->translate('no_selection_description'),
                $this->translate('no_selection_title'),
                AbstractMessage::NOTICE
            );
        } elseif (count($backendUsers) === 0) {
            $this->addFlashMessage(
                $this->translate('empty_description', array($pageId)),
                $this->translate('empty_title', array($pageId)),
                AbstractMessage::INFO
            );
        } else {
            $this->view->assignMultiple(array(
                'pageId' => $pageId,
                'backendUsers' => $backendUsers,
                'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            ));
        }
    }

    /**
     * Translate label for module
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate($key, $arguments = array())
    {
        $label = null;
        if (!empty($key)) {
            $label = LocalizationUtility::translate(
                'backendUserAccessOverview_' . $key,
                'my_user_management',
                $arguments
            );
        }
        return ($label) ? $label : $key;
    }
}