<?php

declare(strict_types=1);

/*
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 */

namespace Oktopuce\SiteGenerator\ContextMenu;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;

/**
 * Item provider for site generator
 */
class SiteGeneratorItemProvider extends AbstractProvider
{
    /**
     * This array contains configuration for site generator item
     * @var array
     */
    protected $itemsConfiguration = [
        'divider10' => [
            'type' => 'divider'
        ],
        'siteGenerator' => [
            'type' => 'item',
            'label' => 'LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:itemProvider.siteGenerator',
            'iconIdentifier' => 'tx_site_generator-sitegenerator',
            'callbackAction' => 'siteGenerator' //name of the function in the JS file
        ]
    ];

    /**
     * The item is only displayed if we're on a page and the pid is the one for new sites
     *
     * @return bool
     */
    public function canHandle(): bool
    {
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator'];
        $sitesPid = GeneralUtility::trimExplode(',', $extensionConfiguration['sitesPid']);
        return ($this->table === 'pages' && in_array($this->identifier, $sitesPid));
    }

    /**
     * Returns the provider priority which is used for determining the order in which providers are processing items
     * to the result array. Highest priority means provider is evaluated first.
     *
     * This item provider should be called after PageProvider which has priority 100.
     *
     * BEWARE: Returned priority should logically not clash with another provider.
     *         Please check @see \TYPO3\CMS\Backend\ContextMenu\ContextMenu::getAvailableProviders() if needed.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 90;
    }

    /**
     * Registers the additional JavaScript RequireJS callback-module which will allow to display a notification
     * whenever the user tries to click on the "Hello World" item.
     * The method is called from AbstractProvider::prepareItems() for each context menu item.
     *
     * @param string $itemName
     * @return array
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        return [
            // BEWARE!!! RequireJS MODULES MUST ALWAYS START WITH "TYPO3/CMS/" (and no "Vendor" segment here)
            'data-callback-module' => 'TYPO3/CMS/SiteGenerator/ContextMenuActions',
            // Here you can also add any other useful "data-" attribute you'd like to use in your JavaScript (e.g. localized messages)
        ];
    }

    /**
     * This method adds the new items at the end of the context menu
     *
     * @param array $items
     * @return array
     */
    public function addItems(array $items): array
    {
        $this->initDisabledItems();
        $localItems = $this->prepareItems($this->itemsConfiguration);
        $items += $localItems;
        return $items;
    }

    /**
     * This method is called for each item this provider adds and checks if given item can be added
     *
     * @param string $itemName
     * @param string $type
     * @return bool
     */
    protected function canRender(string $itemName, string $type): bool
    {
        // checking if item is disabled through TSConfig
        if (in_array($itemName, $this->disabledItems, true)) {
            return false;
        }
        $canRender = false;
        switch ($itemName) {
            case 'siteGenerator':
                $canRender = true;
                break;
            case 'divider10':
                $canRender = true;
                break;
        }
        return $canRender;
    }
}
