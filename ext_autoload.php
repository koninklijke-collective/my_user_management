<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('my_user_management');
return array(
    // Controllers
    'Serfhos\MyUserManagement\Controller\BackendUserController' => $extensionPath . 'Classes/Controller/BackendUserController.php',
    'Serfhos\MyUserManagement\Controller\UserAccessController' => $extensionPath . 'Classes/Controller/UserAccessController.php',

    // Domain: Models
    'Serfhos\MyUserManagement\Domain\Model\BackendUser' => $extensionPath . 'Classes/Domain/Model/BackendUser.php',
    'Serfhos\MyUserManagement\Domain\Model\BackendUserGroup' => $extensionPath . 'Classes/Domain/Model/BackendUserGroup.php',

    // Domain: Repositories
    'Serfhos\MyUserManagement\Domain\Repository\BackendUserGroupRepository' => $extensionPath . 'Classes/Domain/Repository/BackendUserGroupRepository.php',
    'Serfhos\MyUserManagement\Domain\Repository\BackendUserRepository' => $extensionPath . 'Classes/Domain/Repository/BackendUserRepository.php',

    // Services
    'Serfhos\MyUserManagement\Service\AccessService' => $extensionPath . 'Classes/Service/AccessService.php',
    'Serfhos\MyUserManagement\Service\AdministrationService' => $extensionPath . 'Classes/Service/AdministrationService.php',
    'Serfhos\MyUserManagement\Service\ModuleDataStorageService' => $extensionPath . 'Classes/Service/ModuleDataService.php',
);
?>