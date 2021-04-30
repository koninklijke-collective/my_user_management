<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Format;

use Closure;
use KoninklijkeCollective\MyUserManagement\Service\StorageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper: Format Storage Location
 */
final class StoragePathViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('storageId', 'int', '');
        $this->registerArgument('location', 'string', '', false, '/');
    }

    /**
     * Retrieve storage path from given id
     *
     * @param  array  $arguments
     * @param  \Closure  $renderChildrenClosure
     * @param  \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface  $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        return self::getStorageService()->path(
            $arguments['storageId'],
            $arguments['location'] ?? '/'
        );
    }

    /**
     * @return \KoninklijkeCollective\MyUserManagement\Service\StorageService
     */
    protected static function getStorageService(): StorageService
    {
        return GeneralUtility::makeInstance(StorageService::class);
    }
}
