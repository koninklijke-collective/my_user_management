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

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('storageId', 'int', '');
        $this->registerArgument('location', 'string', '', false, '/');
    }

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

    protected static function getStorageService(): StorageService
    {
        return GeneralUtility::makeInstance(StorageService::class);
    }
}
