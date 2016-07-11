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
     * Returns a URL to link to quick command
     *
     * @param string $parameters Is a set of GET params to send to FormEngine
     * @return string URL to FormEngine module + parameters
     */
    public function render($parameters)
    {
        $parameters = GeneralUtility::explodeUrl2Array($parameters);
        $parameters['vC'] = $this->getBackendUserAuthentication()->veriCode();
        $parameters['prErr'] = 1;
        $parameters['uPT'] = 1;

        return BackendUtility::getModuleUrl('tce_db', $parameters);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
