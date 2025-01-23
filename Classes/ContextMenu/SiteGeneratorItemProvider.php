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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;

/**
 * Item provider for site generator.
 */
class SiteGeneratorItemProvider extends AbstractProvider
{
    protected ServerRequest $serverRequest;

    /**
     * @param SiteFinder $siteFinder
     */
    public function __construct(
        private readonly SiteFinder $siteFinder
    ) {
        parent::__construct();
    }

    /**
     * This array contains configuration for site generator item.
     *
     * @var array
     */
    protected $itemsConfiguration = [
        'divider10' => [
            'type' => 'divider',
        ],
        'siteGenerator' => [
            'type' => 'item',
            'label' => 'LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:itemProvider.siteGenerator',
            'iconIdentifier' => 'tx_site_generator-sitegenerator',
            'callbackAction' => 'siteGenerator', //name of the function in the JS file
        ],
    ];

    /**
     * The item is only displayed if we're on a page and the pid is the one for new sites.
     *
     * @return bool
     */
    public function canHandle(): bool
    {
        $sitesPid = $this->getSitesPid();
        return $this->table === 'pages' && in_array($this->identifier, $sitesPid, true);
    }

    /**
     * Get sitesPid from extension configuration (can be override by site configuration).
     *
     * @return array
     */
    public function getSitesPid(): array
    {
        $sitesPid = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator']['sitesPid'];

        try {
            $request = $this->getRequest();
            $id = (int) ($request->getQueryParams()['uid'] ?? $request->getParsedBody()['uid'] ?? 0);

            if ($id) {
                // Retrieve site generator configuration in site configuration
                $site = $this->siteFinder->getSiteByPageId($id);
                $siteConfiguration = $site->getConfiguration();

                $sitesPid = (string) ($siteConfiguration['siteGenerator']['sitesPid'] ?? $sitesPid);
            }
        } catch (SiteNotFoundException $exception) {
            // No site configuration for this page
        }

        return GeneralUtility::trimExplode(',', $sitesPid);
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
     * Registers custom JS module with item onclick behaviour.
     *
     * @throws RouteNotFoundException
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        $attributes = [
            'data-callback-module' => '@oktopuce/site-generator/context-menu-actions',
        ];

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $attributes['data-action-url'] = htmlspecialchars((string) $uriBuilder->buildUriFromRoute('tx_wizard_sitegenerator'));

        return $attributes;
    }

    /**
     * This method adds the new items at the end of the context menu.
     *
     * @param array $items
     *
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
     * This method is called for each item this provider adds and checks if given item can be added.
     *
     * @param string $itemName
     * @param string $type
     *
     * @return bool
     */
    protected function canRender(string $itemName, string $type): bool
    {
        // checking if item is disabled through TSConfig
        if (in_array($itemName, $this->disabledItems, true)) {
            return false;
        }
        return match ($itemName) {
            'divider10', 'siteGenerator' => true,
            default => false,
        };
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
