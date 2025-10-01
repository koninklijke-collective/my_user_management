<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Security\Filter;

use Closure;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Security: Filter all allowed backend groups for current backend user
 */
final class AllowedBackendGroupsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('groups', 'array', 'Current list of groups', false);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $groups = $arguments['groups'] ?? $renderChildrenClosure();
        if (!is_array($groups)) {
            return [];
        }

        $context = GeneralUtility::makeInstance(Context::class);
        if (
            BackendUserGroupPermission::hasConfigured()
            && !$context->getPropertyFromAspect('backend.user', 'isAdmin', false)
        ) {
            foreach ($groups as $key => $group) {
                if (
                    $group instanceof BackendUserGroup
                    && !BackendUserGroupPermission::isConfigured($group->getUid())
                ) {
                    unset($groups[$key]);
                }
            }
        }

        return $groups;
    }
}
