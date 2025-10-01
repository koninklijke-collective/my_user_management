<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use KoninklijkeCollective\MyUserManagement\Functions\TranslateTrait;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service: Backend Controller extending functionality to keep the 'core' changes as minimum
 */
final class BackendUserControllerService implements SingletonInterface
{
    use BackendUserAuthenticationTrait;
    use TranslateTrait;

    private FlashMessageQueue $flashMessageQueue;

    public function setFlashMessageQueue(FlashMessageQueue $flashMessageQueue): self
    {
        $this->flashMessageQueue = $flashMessageQueue;

        return $this;
    }

    public function validateAccessToAction($action)
    {
        $table = match ($action) {
            'index' => BackendUser::TABLE,
            'groups' => BackendUserGroup::TABLE,
            'filemounts' => FileMount::TABLE,
        };

        if (!AccessUtility::beUserHasRightToEditTable($table)) {
            $this->addFlashMessage(
                self::translate('backend_user_no_rights_to_table_description', [BackendUser::TABLE]),
                self::translate('backend_user_no_rights_to_table_title'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR,
            );
        }
    }

    public function manipulateDemandForUser(Demand $demand, FlashMessageQueue $flashMessageQueue): Demand
    {
        if ($this->getBackendUserAuthentication()->isAdmin()) {
            return $demand;
        }

        if ($demand->getUserType() !== Demand::USERTYPE_USERONLY) {
            $this->addFlashMessage(
                self::translate('filter_on_admin_is_not_allowed_description'),
                self::translate('filter_on_admin_is_not_allowed_title'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR,
            );
        }

        $demand->setUserType(Demand::USERTYPE_USERONLY);

        return $demand;
    }

    public function canCreate($action): bool
    {
        $table = match ($action) {
            'index' => BackendUser::TABLE,
            'groups' => BackendUserGroup::TABLE,
            'filemounts' => FileMount::TABLE,
        };

        return AccessUtility::beUserHasRightToAddTable($table);
    }

    private function addFlashMessage(string $messageBody, string $messageTitle = '', ContextualFeedbackSeverity $severity = ContextualFeedbackSeverity::OK, bool $storeInSession = true)
    {
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messageBody,
            $messageTitle,
            $severity,
            $storeInSession
        );

        $this->flashMessageQueue->enqueue($flashMessage);
    }
}
