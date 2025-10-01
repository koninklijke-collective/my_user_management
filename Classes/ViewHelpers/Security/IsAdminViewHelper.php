<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Security;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

final class IsAdminViewHelper extends AbstractConditionViewHelper
{
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        return GeneralUtility::makeInstance(Context::class)
            ->getPropertyFromAspect('backend.user', 'isAdmin');
    }
}
