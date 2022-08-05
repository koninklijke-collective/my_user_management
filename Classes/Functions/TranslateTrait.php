<?php

namespace KoninklijkeCollective\MyUserManagement\Functions;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

trait TranslateTrait
{
    /**
     * Translate key for local extension from locallang_be.xlf
     *
     * @see EXT:my_user_management/Resources/Private/Language/locallang_be.xlf
     * @param  array  $arguments
     * @param  string  $key
     * @return string
     */
    protected static function translate(string $key, array $arguments = []): string
    {
        if (empty($key)) {
            return 'No translation key given';
        }

        $translation = LocalizationUtility::translate(
            'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf'
            . ':' . $key,
            'my_user_management',
            $arguments
        );

        if (empty($translation)) {
            return 'No translation for key: ' . $key;
        }

        return $translation;
    }
}
