<?php

use Oktopuce\SiteGenerator\Controller\SiteGeneratorController;

/**
 * Definitions for routes provided by EXT:site_generator
 */
return [
    'tx_wizard_sitegenerator' => [
        'path' => '/wizard/sitegenerator',
        'extensionName' => 'SiteGenerator',
        'controllerActions' => [
            SiteGeneratorController::class => [
                'getDataFirstStep',
                'getDataSecondStep',
                'generateSite'
            ],
        ]
    ]
];
