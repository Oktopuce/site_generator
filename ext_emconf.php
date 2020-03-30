<?php

/*********************************************************************
 * Extension Manager/Repository config file for ext: "site_generator"
 *********************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site generator / tree model duplicator',
    'description' => 'Site generator wizard used to generate mini-website or duplicate tree model, it will automatically create associated BE/FE groups, create directories with associated files mount, add domain name, update Typoscript configuration (folders/pages ID and TCEMAIN.clearCacheCmd), update slugs.',
    'category' => 'services',
    'author' => 'Florian Rival',
    'author_email' => 'florian.typo3@oktopuce.fr',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.9.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
