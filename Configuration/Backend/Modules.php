<?php

use Oktopuce\SiteGenerator\Controller\SiteGeneratorController;

/*
 * Definitions for routes provided by EXT:site_generator
 */
return [
    'tx_wizard_sitegenerator' => [
        'path' => '/wizard/sitegenerator',
        'extensionName' => 'SiteGenerator',
        'access' => 'user',
        'appearance' => [
            'renderInModuleMenu' => false,
        ],
        'labels' => [
            'title' => 'LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:itemProvider.siteGenerator',
        ],

        'controllerActions' => [
            SiteGeneratorController::class => [
                'getDataFirstStep',
                'getDataSecondStep',
                'generateSite',
            ],
        ],
    ],
];
