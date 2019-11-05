<?php

namespace KoninklijkeCollective\MyUserManagement\Backend\Form\Wizard;

use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount;
use KoninklijkeCollective\MyUserManagement\Utility\AccessUtility;
use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;

/**
 * Suggest Receiver: Backend Management
 * Can be used to allow custom suggests wizards;
 *
 * == Example: ============================================================================================================
 * \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser::TABLE => [
 *   'receiverClass' => 'KoninklijkeCollective\\MyUserManagement\\Backend\\Form\\Wizard\\SuggestBackendManagementReceiver',
 *   'searchCondition' => 'admin = 0',
 *   'searchWholePhrase' => true,
 *   'additionalSearchFields' => 'username,realName,email',
 *   'orderBy' => 'username',
 * ],
 * ========================================================================================================================
 */
class SuggestBackendManagementReceiver extends SuggestWizardDefaultReceiver
{

    /**
     * Make sure editor can search through backend user
     *
     * @param array $row
     * @param integer $uid
     * @return boolean
     */
    protected function checkRecordAccess($row, $uid)
    {
        if (in_array($this->table, [BackendUser::TABLE, BackendUserGroup::TABLE, FileMount::TABLE])) {
            return AccessUtility::beUserHasRightToSeeTable($this->table);
        }
        return parent::checkRecordAccess($row, $uid);
    }
}
