<?php
namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller: FileMount
 *
 * @package KoninklijkeCollective\MyUserManagement\Controller
 */
class FileMountController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * The fileMountRepository
     *
     * @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository
     * @inject
     */
    protected $fileMountRepository;

    /**
     * Action: List all file mounts
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->getBackendUserAuthentication()->check('tables_modify', FileMountRepository::TABLE) === false) {
            $this->addFlashMessage(
                $this->translate('access_users_table_not_allowed_description', array(FileMountRepository::TABLE)),
                $this->translate('access_users_table_not_allowed_title'),
                AbstractMessage::ERROR
            );
        }

        $this->view->assign(
            'returnUrl',
            rawurlencode(BackendUtility::getModuleUrl('myusermanagement_MyUserManagementFilemountadmin'))
        );

        $fileMounts = $this->fileMountRepository->findAll();
        if (count($fileMounts) === 0) {
            $this->addFlashMessage(
                $this->translate('empty_description'),
                $this->translate('empty_title'),
                AbstractMessage::INFO
            );
        } else {
            $this->view->assign('fileMounts', $fileMounts);
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
                'backendFileMountOverview_' . $key,
                'my_user_management',
                $arguments
            );
        }
        return ($label) ? $label : $key;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

}
