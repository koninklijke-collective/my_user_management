<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'My Backend User Management',
    'description' => 'A module that makes it possible for editors to maintain backend users.',
    'category' => 'module',
    'version' => '2.0.4',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-7.99.99',
            'beuser' => '6.2.0-7.99.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
