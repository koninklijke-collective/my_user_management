<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Displays link with sprite icon with confirm message
 *
 * @package KoninklijkeCollective\MyRedirects\ViewHelpers
 */
class LinkConfirmViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('link', 'string', 'Arguments', false);
        $this->registerArgument('message', 'string', 'Arguments', false);
        $this->registerArgument('title', 'string', 'Arguments', false);
        $this->registerArgument('class', 'string', 'Arguments', false);
        $this->registerArgument('icon', 'string', 'Arguments', false);
    }


    /**
     * Render confirm link with sprite icon
     *
     * @return string
     */
    public function render()
    {
        $icon = 'actions-edit-delete';
        $link = GeneralUtility::_GP('link');
        $message = GeneralUtility::_GP('message');
        $title = GeneralUtility::_GP('title');
        $class = GeneralUtility::_GP('class');

        if (!empty($link)) {
            /** @var \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory */
            $iconFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconFactory::class);
            $attributes = [
                'href' => $link,
                'data-severity' => 'warning',
                'data-title' => $title,
                'data-content' => $message,
                'data-button-close-text' => $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_common.xlf:cancel'),
                'class' => 'btn btn-default t3js-modal-trigger' . ($class ? ' ' . $class : ''),
            ];
            return '<a ' . GeneralUtility::implodeAttributes($attributes, true, true) . '>'
            . $iconFactory->getIcon($icon, \TYPO3\CMS\Core\Imaging\Icon::SIZE_SMALL)
            . '</a>';
        }
        return '';
    }
}
