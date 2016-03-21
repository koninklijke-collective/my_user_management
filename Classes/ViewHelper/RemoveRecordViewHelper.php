<?php
namespace Serfhos\MyUserManagement\ViewHelper;

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

        // Make sure record_edit module is available
        if (GeneralUtility::compat_version('7.0')) {
            $url = BackendUtility::getModuleUrl('tce_db', $parameters);
        } else {
            $url = 'tce_db.php?' . GeneralUtility::implodeArrayForUrl('', $parameters);
        }

        return $url . BackendUtility::getUrlToken('tceAction');
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
