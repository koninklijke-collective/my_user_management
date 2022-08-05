<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Security;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

final class IsAdminViewHelper extends AbstractConditionViewHelper
{
    /**
     * @param  array  $arguments
     * @param  \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface  $renderingContext
     * @return bool
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $userAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');

        return $userAspect->isAdmin();
    }
}
