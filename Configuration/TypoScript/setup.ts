# Module configuration
module.tx_myusermanagement {
    persistence {
        storagePid = 0
        classes {
            Serfhos\MyUserManagement\Domain\Model\BackendUser {
                mapping {
                    tableName = be_users
                    columns {
                        allowed_languages.mapOnProperty = allowedLanguages
                        file_mountpoints.mapOnProperty = fileMountPoints
                        db_mountpoints.mapOnProperty = dbMountPoints
                        usergroup.mapOnProperty = backendUserGroups
                    }
                }
            }

            Serfhos\MyUserManagement\Domain\Model\BackendUserGroup {
                mapping {
                    tableName = be_groups
                    columns {
                        subgroup.mapOnProperty = subGroups
                        db_mountpoints.mapOnProperty = dbMountPoints
                        hidden.mapOnProperty = isDisabled
                    }
                }
            }

            Serfhos\MyUserManagement\Domain\Model\FileMount {
                mapping {
                    tableName = sys_filemounts
                    columns {
                        title.mapOnProperty = title
                        path.mapOnProperty = path
                        base.mapOnProperty = storage
                        hidden.mapOnProperty = isDisabled
                    }
                }
            }
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