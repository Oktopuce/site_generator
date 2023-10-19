<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;

defined('TYPO3') || die('Access denied.');

call_user_func(
    static function ($extKey) {
        // Provide icon for page tree, list view, ... :
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        $iconRegistry->registerIcon(
            'tx_site_generator-sitegenerator',
            BitmapIconProvider::class,
            ['source' => 'EXT:' . $extKey . '/Resources/Public/Icons/SiteGenerator.svg']
        );
    },
    'site_generator'
);
