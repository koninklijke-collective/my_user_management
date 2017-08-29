<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'My Backend User Management',
    'description' => 'A module that makes it possible for editors to maintain backend users.',
    'category' => 'module',
    'version' => '3.3.4',
    'state' => 'stable',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearcacheonload' => true,
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'internal' => '',
    'modify_tables' => '',
    'lockType' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'beuser' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'KoninklijkeCollective\\MyUserManagement\\' => 'Classes',
        ],
    ],
];
