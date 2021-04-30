<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Security;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserActionPermission;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

final class IsActionAllowedViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initializes the "action" argument.
     * Renders <f:then> child if the current logged in BE user has access to the specific action
     * otherwise renders <f:else> child.
     */
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

    /**
     * @param  array  $arguments
     * @param  \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface  $renderingContext
     * @return bool
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $action = $arguments['action'];

        $userAspect = GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');
        if (!$userAspect->isLoggedIn()) {
            return false;
        }

        if (is_numeric($action)) {
            $id = self::getKeyFromInt($action);
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
     *
     * @param  int  $value
     * @return int|null
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
     *
     * @param  string  $value
     * @return int|null
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
