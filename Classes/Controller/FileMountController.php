<?php
namespace Serfhos\MyUserManagement\Controller;

/**
 * File mount controller
 *
 * @package my_user_management
 * @author Sebastiaan de Jonge <office@sebastiaandejonge.com>, SebastiaanDeJonge.com
 */
class FileMountController extends AbstractBackendController
{

    /**
     * The fileMountRepository
     *
     * @var \Serfhos\MyUserManagement\Domain\Repository\FileMountRepository
     * @inject
     */
    protected $fileMountRepository;

    /**
     * Override used class
     *
     * @var \Serfhos\MyUserManagement\Domain\Model\FileMountModuleData
     */
    protected $moduleData;

    /**
     * List of all file mounts
     *
     * @param \Serfhos\MyUserManagement\Domain\Model\FileMountDemand $fileMountDemand
     * @return void
     */
    public function listAction(\Serfhos\MyUserManagement\Domain\Model\FileMountDemand $fileMountDemand = null)
    {
        if ($fileMountDemand === null) {
            $fileMountDemand = $this->moduleData->getDemand();
        } else {
            $this->moduleData->setDemand($fileMountDemand);
        }

        $this->view->assignMultiple(
            array(
                'fileMountDemand' => $fileMountDemand,
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
    public function detailAction($fileMount)
    {
        $fileMount = $this->fileMountRepository->findByUid($fileMount);
        $this->view->assignMultiple(
            array(
                'fileMount' => $fileMount
            )
        );
    }

    /**
     * Returns generic module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return 'MyUserManagementMyusermanagement_MyUserManagementFilemountadmin';
    }
}