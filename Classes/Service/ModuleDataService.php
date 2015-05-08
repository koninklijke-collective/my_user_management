<?php
namespace Serfhos\MyUserManagement\Service;

/**
 * Module data storage service.
 * Used to store and retrieve module state (eg. checkboxes, selections).
 *
 * @package Serfhos\MyUserManagement\Service
 */
class ModuleDataStorageService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    const PREFIX = 'tx_myusermanagement';

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Loads module data for user settings or returns a fresh object initially
     *
     * @param string $key
     * @return mixed
     */
    public function loadModuleData($key = '')
    {
        if (empty($this->key)) {
            $this->key = $key;
        }

        $moduleData = $this->getBackendUserAuthentication()->getModuleData(self::PREFIX . $this->key);
        if (empty($moduleData) || !$moduleData) {
            switch ($this->key) {
                case 'MyUserManagementMyusermanagement_MyUserManagementGroupadmin':
                    $moduleData = $this->objectManager->get('Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroupModuleData');
                    break;
                case 'MyUserManagementMyusermanagement_MyUserManagementFilemountadmin':
                    $moduleData = $this->objectManager->get('Serfhos\\MyUserManagement\\Domain\\Model\\FileMountModuleData');
                    break;
                case 'MyUserManagementMyusermanagement_MyUserManagementUseradmin':
                default:
                    $moduleData = $this->objectManager->get('TYPO3\\CMS\\Beuser\\Domain\\Model\\ModuleData');
                    break;
            }
        } else {
            $moduleData = @unserialize($moduleData);
        }

        //  Force module data to only display non-admin users before returning
        if ($moduleData instanceof \TYPO3\CMS\Beuser\Domain\Model\ModuleData) {
            if (!$this->getBackendUserAuthentication()->isAdmin()) {
                $demand = $moduleData->getDemand();
                $demand->setUserType(2);
                $moduleData->setDemand($demand);
            }
        }

        return $moduleData;
    }

    /**
     * Sets the key
     *
     * @param string $key
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Persists serialized module data to user settings
     *
     * @param mixed $moduleData
     * @return void
     */
    public function persistModuleData($moduleData)
    {
        $this->getBackendUserAuthentication()->pushModuleData(self::PREFIX . $this->key, serialize($moduleData));
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}