<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers;

use Closure;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Retrieve page information for given id
 */
final class PageInfoViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('pageId', 'integer', 'Page ID to retrieve information about', true);
        $this->registerArgument('as', 'string', 'Variable to use', false, 'page');
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $variableProvider = $renderingContext->getVariableProvider();
        $variableProvider->add($arguments['as'], self::getPageRepository()->getPage($arguments['pageId']));
        $output = $renderChildrenClosure();
        $variableProvider->remove($arguments['as']);

        return $output;
    }

    protected static function getPageRepository(): PageRepository
    {
        return GeneralUtility::makeInstance(PageRepository::class);
    }
}
