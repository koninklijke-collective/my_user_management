<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Edit Record ViewHelper, see FormEngine logic
 *
 * @internal
 */
class EditRecordViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('parameters', 'string', 'Arguments', false);
    }


    /**
     * Returns a URL to link to FormEngine
     *
     * @return string URL to FormEngine module + parameters
     * @see \TYPO3\CMS\Backend\Utility\BackendUtility::getModuleUrl()
     */
    public function render()
    {
        $parameters = GeneralUtility::_GP('parameters');
        $parameters = GeneralUtility::explodeUrl2Array($parameters);
        return BackendUtility::getModuleUrl('record_edit', $parameters);
    }
}
