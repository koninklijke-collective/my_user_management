# Define classes in default extbase config
config.tx_extbase.persistence.classes {
    KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser < config.tx_extbase.persistence.classes.TYPO3\CMS\Beuser\Domain\Model\BackendUser

    KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup < config.tx_extbase.persistence.classes.TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup
    KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup {
        mapping {
            columns {
                db_mountpoints.mapOnProperty = dbMountPoints
                hidden.mapOnProperty = isDisabled
            }
        }
    }

    KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount < config.tx_extbase.persistence.classes.TYPO3\CMS\Extbase\Domain\Model\FileMount
    KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount {
        mapping {
            columns {
                base.mapOnProperty = storage
                hidden.mapOnProperty = isDisabled
            }
        }
    }
}

# Module configuration
module.tx_myusermanagement {
    view {
        templateRootPaths {
            10 = EXT:beuser/Resources/Private/Templates
            20 = EXT:my_user_management/Resources/Private/Templates
        }

        partialRootPaths {
            10 = EXT:beuser/Resources/Private/Partials
            20 = EXT:my_user_management/Resources/Private/Partials
        }

        layoutRootPaths {
            10 = EXT:beuser/Resources/Private/Layouts
            20 = EXT:my_user_management/Resources/Private/Layouts
        }
    }

    settings {
        // This is a dummy entry. It is used in  Tx_Beuser_Controller_BackendUserController
        // to test that some TypoScript configuration is set.
        // This entry can be removed if extbase setup is made frontend TS independant
        // or if there are other settings set.
        dummy = foo
    }
}