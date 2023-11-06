<?php

namespace KoninklijkeCollective\MyUserManagement\Controller;

use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Beuser\Domain\Model\Demand;

#[Controller]
final class UserManagementController extends \TYPO3\CMS\Beuser\Controller\BackendUserController
{
    use TranslateTrait;
    use BackendUserAuthenticationTrait;

    public function indexAction(Demand $demand = null, int $currentPage = 1, string $operation = ''): ResponseInterface
    {
        // @todo implement the OgBackendUserController
        return parent::indexAction($demand, $currentPage, $operation);
    }
}
