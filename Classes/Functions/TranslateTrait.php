<?php

namespace KoninklijkeCollective\MyUserManagement\Functions;

use KoninklijkeCollective\MyUserManagement\Utility\ConfigurationUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

trait TranslateTrait
{

    /**
     * Translate key for local extension from locallang_be.xlf
     *
     * @param  string  $key
     * @param  array  $arguments
     * @return string
     * @see EXT:my_user_management/Resources/Private/Language/locallang_be.xlf
     */
    protected static function translate(string $key, array $arguments = []): string
    {
        if (empty($key)) {
            return 'No translation key given';
        }

        $translation = LocalizationUtility::translate(
            'LLL:EXT:' . ConfigurationUtility::EXTENSION . '/Resources/Private/Language/locallang_be.xlf'
            . ':' . $key,
            ConfigurationUtility::EXTENSION,
            $arguments
        );

        if (empty($translation)) {
            return 'No translation for key: ' . $key;
        }

        return $translation;
    }
}
