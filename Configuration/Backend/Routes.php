<?php

/**
 * Definitions for routes provided by EXT:site_generator
 */
return [
    // Register click menu entry point
    'wizard_sitegenerator' => [
        'path' => '/wizard/sitegenerator/',
        'target' => \Oktopuce\SiteGenerator\Controller\SiteGeneratorController::class . '::dispatch'
    ]
];
