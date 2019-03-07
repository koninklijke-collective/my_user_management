<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Retrieve page information
 *
 * @package KoninklijkeCollective\MyUserManagement\ViewHelpers
 */
class PageInfoViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('pageId', 'integer', 'Arguments', false);
    }


    /**
     * Retrieve page details from given page id
     *

     * @return string Rendered string
     */
    public function render()
    {
        $id = GeneralUtility::_GP('id');

        $pageRecord = BackendUtility::readPageAccess($id, $GLOBALS['BE_USER']->getPagePermsClause(Permission::PAGE_SHOW));
        // Add icon with context menu, etc:
        /** @var IconFactory $iconFactory */
        if ($pageRecord['uid']) {
            $this->templateVariableContainer->add('page', $this->getPageRepository()->getPage($pageRecord['pageId']));
            $output = $this->renderChildren();
            $this->templateVariableContainer->remove('page');
            return $output;
        }

    }

    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {

        if ($this->pageRepository === null) {
            $this->objectManager->get(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        }
        return $this->pageRepository;
    }
}
