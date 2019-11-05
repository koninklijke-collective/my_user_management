<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\Exception\NotImplementedMethodException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generic trait for Permissions access Backend User Groups
 *
 * @usage $GLOBALS['TYPO3_CONF_VARS']['BE']['customPermOptions'][class::KEY]
 * @see \TYPO3\CMS\Backend\Form\FormDataProvider\AbstractItemProvider::addItemsFromSpecial
 */
trait PermissionTrait
{
    /** @var array */
    protected static $configured;

    /** @var bool cached admin lookup from aspect context */
    protected static $isAdmin;

    /**
     * @return string
     */
    protected static function key(): string
    {
        if (static::KEY === null) {
            throw new NotImplementedMethodException('Key should return the permission key in custom_options');
        }

        return static::KEY;
    }

    /**
     * @return bool
     */
    protected static function userIsAdmin(): bool
    {
        if (static::$isAdmin === null) {
            try {
                static::$isAdmin = GeneralUtility::makeInstance(Context::class)
                    ->getPropertyFromAspect('backend.user', 'isAdmin', false);
            } catch (AspectNotFoundException $e) {
                static::$isAdmin = false;
            }
        }

        return static::$isAdmin;
    }

    /**
     * Get configured options based on current backend user
     *
     * @return array
     */
    public static function getConfigured(): array
    {
        if (static::$configured === null) {
            static::$configured = [];

            // If admin, dont return any configured options
            if (static::userIsAdmin()) {
                return [];
            }

            /** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser */
            $backendUser = $GLOBALS['BE_USER'];
            if ($backendUser === null) {
                return [];
            }

            $options = $backendUser->groupData['custom_options'] ?? '';
            foreach (GeneralUtility::trimExplode(',', $options, true) as $value) {
                // Check if custom option value is a key for this object
                if (strpos($value, static::key()) === 0) {
                    // Only return id; remove `my_custom_key` and cast as int
                    $id = (int)substr($value, strlen(static::key()) + 1);
                    if ($id > 0) {
                        static::$configured[] = $id;
                    }
                }
            }
        }

        return static::$configured;
    }

    /**
     * @return bool
     */
    public static function hasConfigured(): bool
    {
        return static::getConfigured() !== [];
    }

    /**
     * Check if identifier is configured by backend user
     *
     * @param  integer  $identifier
     * @return boolean
     */
    public static function isConfigured(int $identifier): bool
    {
        return static::userIsAdmin() || in_array($identifier, static::getConfigured(), true);
    }
}
