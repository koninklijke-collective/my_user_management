<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'My Backend User Management',
    'description' => 'A module that makes it possible for editors to maintain backend users.',
    'category' => 'module',
    'shy' => FALSE,
    'version' => '1.1.2',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'alpha',
    'uploadfolder' => FALSE,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => TRUE,
    'lockType' => '',
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'CGLcompliance' => NULL,
    'CGLcompliance_note' => NULL,
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.0.0-6.2.99',
            'beuser' => '6.0.0-6.2.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);