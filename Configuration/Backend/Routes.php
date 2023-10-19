<?php

use Oktopuce\SiteGenerator\Controller\SiteGeneratorController;

/**
 * Definitions for routes provided by EXT:site_generator
 */
return [
    // Register click menu entry point
    'tx_wizard_sitegenerator' => [
        'path' => '/wizard/sitegenerator',
        'target' => SiteGeneratorController::class . '::dispatch',
    ]
];
