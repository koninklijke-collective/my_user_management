<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Retrieve page information for given id
 */
final class PageInfoViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    use CompileWithRenderStatic;

    /** @var boolean */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('pageId', 'integer', 'Page ID to retrieve information about', true);
        $this->registerArgument('as', 'string', 'Variable to use', false, 'page');
    }

    /**
     * @param  array  $arguments
     * @param  \Closure  $renderChildrenClosure
     * @param  \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface  $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $variableProvider = $renderingContext->getVariableProvider();
        $variableProvider->add($arguments['as'], static::getPageRepository()->getPage($arguments['pageId']));
        $output = $renderChildrenClosure();
        $variableProvider->remove($arguments['as']);

        return $output;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected static function getPageRepository(): PageRepository
    {
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(PageRepository::class);
    }
}
