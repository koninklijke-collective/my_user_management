<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('my_user_management');
return array(
    // Controllers
    'Serfhos\\MyUserManagement\\Controller\\AbstractBackendController' => $extensionPath . 'Classes/Controller/AbstractBackendController.php',
    'Serfhos\\MyUserManagement\\Controller\\BackendUserController' => $extensionPath . 'Classes/Controller/BackendUserController.php',
    'Serfhos\\MyUserManagement\\Controller\\BackendUserGroupController' => $extensionPath . 'Classes/Controller/BackendUserGroupController.php',
    'Serfhos\\MyUserManagement\\Controller\\FileMountController' => $extensionPath . 'Classes/Controller/FileMountController.php',
    'Serfhos\\MyUserManagement\\Controller\\UserAccessController' => $extensionPath . 'Classes/Controller/UserAccessController.php',

    // Domain: Models
    'Serfhos\\MyUserManagement\\Domain\\Model\\BackendUser' => $extensionPath . 'Classes/Domain/Model/BackendUser.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroup' => $extensionPath . 'Classes/Domain/Model/BackendUserGroup.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroupDemand' => $extensionPath . 'Classes/Domain/Model/BackendUserGroupDemand.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\BackendUserGroupModuleData' => $extensionPath . 'Classes/Domain/Model/BackendUserGroupModuleData.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\FileMount' => $extensionPath . 'Classes/Domain/Model/FileMount.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\FileMountDemand' => $extensionPath . 'Classes/Domain/Model/FileMountDemand.php',
    'Serfhos\\MyUserManagement\\Domain\\Model\\FileMountModuleData' => $extensionPath . 'Classes/Domain/Model/FileMountModuleData.php',

    // Domain: Repositories
    'Serfhos\\MyUserManagement\\Domain\\Repository\\BackendUserGroupRepository' => $extensionPath . 'Classes/Domain/Repository/BackendUserGroupRepository.php',
    'Serfhos\\MyUserManagement\\Domain\\Repository\\BackendUserRepository' => $extensionPath . 'Classes/Domain/Repository/BackendUserRepository.php',
    'Serfhos\\MyUserManagement\\Domain\\Repository\\FileMountRepository' => $extensionPath . 'Classes/Domain/Repository/FileMountRepository.php',

    // Services
    'Serfhos\\MyUserManagement\\Service\\AccessService' => $extensionPath . 'Classes/Service/AccessService.php',
    'Serfhos\\MyUserManagement\\Service\\AdministrationService' => $extensionPath . 'Classes/Service/AdministrationService.php',
    'Serfhos\\MyUserManagement\\Service\\ModuleDataStorageService' => $extensionPath . 'Classes/Service/ModuleDataService.php',
);