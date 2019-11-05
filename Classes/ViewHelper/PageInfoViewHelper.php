<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Retrieve page information
 */
class PageInfoViewHelper extends AbstractViewHelper
{

    /** @var boolean */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * Retrieve page details from given page id
     *
     * @param  integer  $pageId
     * @param  string  $as
     * @return string Rendered string
     */
    public function render($pageId, $as = 'page')
    {
        $this->templateVariableContainer->add($as, $this->getPageRepository()->getPage($pageId));
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        return $output;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        if ($this->pageRepository === null) {
            $this->objectManager->get(PageRepository::class);
        }

        return $this->pageRepository;
    }
}
