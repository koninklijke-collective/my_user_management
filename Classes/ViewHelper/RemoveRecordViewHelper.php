<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelper;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Remove Record ViewHelper, see FormEngine logic
 *
 * @internal
 */
class RemoveRecordViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
     * Returns a URL to link to quick command
     *
     * @return string URL to FormEngine module + parameters
     */
    public function render()
    {
        $parameters = GeneralUtility::_GP('parameters');

        $parameters = GeneralUtility::explodeUrl2Array($parameters);
        $parameters['prErr'] = 1;
        $parameters['uPT'] = 1;

        return BackendUtility::getModuleUrl('tce_db', $parameters);
    }

}
