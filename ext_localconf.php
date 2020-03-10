<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
	function($extKey)
	{
		if (TYPO3_MODE === 'BE') {
			// Add a context menu item for site generation
			$GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1517927406] =
				\Oktopuce\SiteGenerator\ContextMenu\SiteGeneratorItemProvider::class;

			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess'][] = \Oktopuce\SiteGenerator\Hook\BackendControllerHook::class . '->addJavaScript';

            // Provide icon for page tree, list view, ... :
            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
            $iconRegistry->registerIcon(
                'tx_site_generator-sitegenerator',
                TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                ['source' => 'EXT:' . $extKey . '/Resources/Public/Icons/SiteGenerator.svg']
            );
        }
    },
    'site_generator'
);
