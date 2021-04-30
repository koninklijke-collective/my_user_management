<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Controller: FileMount
 *
 * @todo check for refactoring
 */
final class FileMountController extends ActionController
{
    use TranslateTrait;

    /** @var \KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository */
    protected $fileMountRepository;

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Repository\FileMountRepository  $fileMountRepository
     */
    public function __construct(FileMountRepository $fileMountRepository)
    {
        $this->fileMountRepository = $fileMountRepository;
    }

    /**
     * Assign default variables to view
     *
     * @param  \TYPO3\CMS\Extbase\Mvc\View\ViewInterface  $view
     */
    protected function initializeView(ViewInterface $view): void
    {
        $view->assignMultiple([
            'shortcutLabel' => 'MyFileMount',
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }

    /**
     * Action: List all file mounts
     *
     * @return void
     */
    public function indexAction(): void
    {
        if (AccessUtility::beUserHasRightToEditTable(FileMount::TABLE) === false) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUserGroup::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                AbstractMessage::ERROR
            );
        }

        $fileMounts = $this->fileMountRepository->findAll();
        $this->view->assign('fileMounts', $fileMounts);
    }
}
