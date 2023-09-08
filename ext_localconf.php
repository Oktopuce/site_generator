<?php
use Oktopuce\SiteGenerator\ContextMenu\SiteGeneratorItemProvider;
use Oktopuce\SiteGenerator\Hook\BackendControllerHook;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
defined('TYPO3') || die('Access denied.');

call_user_func(
    function ($extKey) {
        // Add a context menu item for site generation
        $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1517927406] =
            SiteGeneratorItemProvider::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess'][] = BackendControllerHook::class . '->addJavaScript';

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
