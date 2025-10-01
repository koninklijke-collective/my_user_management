<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Security;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Renders <f:then> child if the current logged in BE user has access to the specific action
 * otherwise renders <f:else> child.
 */
final class IsActionAllowedViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'action',
            'string',
            'uid or its constant name of BackendUserActionPermission',
            false
        );
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $action = $arguments['action'];

        if (!GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('backend.user', 'isLoggedIn')) {
            return false;
        }

        if (MathUtility::canBeInterpretedAsInteger($action)) {
            $id = self::getKeyFromInt((int)$action);
        } else {
            $id = self::getKeyFromString($action);
        }

        if ($id === null) {
            return false;
        }

        return BackendUserActionPermission::isConfigured($id);
    }

    /**
     * Validate incoming key to allow in lookup
     */
    protected static function getKeyFromInt(int $value): ?int
    {
        if (!array_key_exists($value, BackendUserActionPermission::getItems())) {
            return null;
        }

        return $value;
    }

    /**
     * Get key from constant string when defined in BackendUserActionPermission
     */
    protected static function getKeyFromString(string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Sanitize incoming string
        $value = preg_replace('/\W/u', '', $value);
        if ($value === '') {
            return null;
        }

        // Combine expected constant key
        $constant = BackendUserActionPermission::class . '::ACTION_' . strtoupper($value);
        if (!defined($constant)) {
            return null;
        }

        return constant($constant);
    }
}
