<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'My Backend User Management',
    'description' => 'A module that makes it possible for editors to maintain backend users.',
    'category' => 'module',
    'shy' => false,
    'version' => '1.1.3',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadFolder' => false,
    'modify_tables' => '',
    'clearCacheOnLoad' => true,
    'lockType' => '',
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.0.0-6.2.99',
            'beuser' => '6.0.0-6.2.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);