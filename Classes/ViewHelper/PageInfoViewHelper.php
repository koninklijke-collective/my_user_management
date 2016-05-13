<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

/**
 * Retrieve page information
 *
 * @package KoninklijkeCollective\MyUserManagement\ViewHelpers
 */
class PageInfoViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * Retrieve page details from given page id
     *
     * @param integer $pageId
     * @param string $as
     * @return string Rendered string
     */
    public function render($pageId, $as = 'page')
    {
        $this->templateVariableContainer->add($as, $this->pageRepository->getPage($pageId));
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove($as);
        return $output;
    }
}
