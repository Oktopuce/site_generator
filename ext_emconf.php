<?php

/*********************************************************************
 * Extension Manager/Repository config file for ext: "site_generator"
 *********************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site generator / tree model duplicator',
    'description' => 'Site generator wizard : used to generate mini-website or duplicate tree model and affect groups, create folder and so on',
    'category' => 'services',
    'author' => 'Florian Rival',
    'author_email' => 'florian.typo3@oktopuce.fr',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
