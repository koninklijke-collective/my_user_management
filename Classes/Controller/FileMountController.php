<?php
namespace Serfhos\MyUserManagement\Controller;

/***************************************************************
 * Copyright notice
 *
 * (c) 2013 Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * File mount controller
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * The fileMountRepository
     *
     * @var \Serfhos\MyUserManagement\Domain\Repository\FileMountRepository
     * @inject
     */
    protected $fileMountRepository;

    /**
     * @var \Serfhos\MyUserManagement\Domain\Model\FileMountModuleData
     */
    protected $moduleData;

    /**
     * @var \Serfhos\MyUserManagement\Service\ModuleDataStorageService
     * @inject
     */
    protected $moduleDataStorageService;

    /**
     * Load and persist module data
     *
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
        $this->moduleData = $this->moduleDataStorageService->loadModuleData('_file_mount');

        // We "finally" persist the module data.
        try {
            parent::processRequest($request, $response);
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
        } catch (\TYPO3\CMS\Extbase\Mvc\Exception\StopActionException $e) {
            $this->moduleDataStorageService->persistModuleData($this->moduleData);
            throw $e;
        }
    }

    /**
     * Initialize actions
     *
     * @return void
     * @throws \RuntimeException
     */
    public function initializeAction() {
        // @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
        // and settings. Thus, we need a TypoScript root template, that then loads the
        // ext_typoscript_setup.txt file of this module. This is nasty, but can not be
        // circumvented until there is a better solution in extbase.
        // For now we throw an exception if no settings are detected.
        if (empty($this->settings)) {
            throw new \RuntimeException('No settings detected. This module can not work then. This usually happens if there is no frontend TypoScript template with root flag set. ' . 'Please create a frontend page with a TypoScript root template.', 1344375003);
        }
    }

    /**
     * List of all file mounts
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMountDemand $fileMountDemand
     * @return void
     */
    public function listAction(\Serfhos\MyUserManagement\Domain\Model\FileMountDemand $fileMountDemand = NULL) {
        if ($fileMountDemand === NULL) {
            $fileMountDemand = $this->moduleData->getDemand();
        } else {
            $this->moduleData->setDemand($fileMountDemand);
        }

        $this->view->assignMultiple(
            array(
                'fileMountDemand' => $fileMountDemand,
                'returnUrl' => 'mod.php?M=MyUserManagementMyusermanagement_MyUserManagementFilemountadmin',
                'fileMounts' => $this->fileMountRepository->findByDemand($fileMountDemand)
            )
        );
    }

    /**
     * Detailed information of a file mount
     *
     * @param int $fileMount (int because if the object is hidden, it will
     * @return void
     */
    public function detailAction($fileMount) {
        $fileMount = $this->fileMountRepository->findByUid($fileMount);
        $this->view->assignMultiple(
            array(
                'fileMount' => $fileMount
            )
        );
    }

}