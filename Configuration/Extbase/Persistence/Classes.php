<?php

declare(strict_types=1);

return [
    \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
        'properties' => [
            'allowedLanguages' => [
                'fieldName' => 'allowed_languages',
            ],
            'fileMountPoints' => [
                'fieldName' => 'file_mountpoints',
            ],
            'dbMountPoints' => [
                'fieldName' => 'db_mountpoints',
            ],
            'backendUserGroups' => [
                'fieldName' => 'usergroup',
            ],
            'createdBy' => [
                'fieldName' => 'cruser_id',
            ],
        ],
    ],
    \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup::class => [
        'tableName' => 'be_groups',
        'properties' => [
            'subGroups' => [
                'fieldName' => 'subgroup',
            ],
            'databaseMountPoints' => [
                'fieldName' => 'db_mountpoints',
            ],
            'fileMountPoints' => [
                'fieldName' => 'file_mountpoints',
            ],
        ],
    ],
    \KoninklijkeCollective\MyUserManagement\Domain\Model\FileMount::class => [
        'tableName' => 'sys_filemounts',
        'properties' => [
            'title' => [
                'fieldName' => 'title',
            ],
            'path' => [
                'fieldName' => 'path',
            ],
            'isAbsolutePath' => [
                'fieldName' => 'base',
            ],
            'storage' => [
                'fieldName' => 'base',
            ],
        ],
    ],
];
