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

namespace Oktopuce\SiteGenerator\Wizard;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Base class for all states : logger, extension configuration, ...
 */
class StateBase
{
    /**
     * @var LoggerInterface|null|Logger
     */
    protected LoggerInterface|null|Logger $logger = null;
    protected readonly SiteFinder $siteFinder;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
    }

    public function injectSiteFinder(SiteFinder $siteFinder): void
    {
        $this->siteFinder = $siteFinder;
    }

    /*
     * Adds a log record
     *
     * @param int|string $level Log level. Value according to \Psr\Log\LogLevel. Alternatively accepts a string.
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @return void
     */
    public function log($level, $message, array $data = []): void
    {
        $this->logger->log($level, "Site generator : " . $message, $data);
    }

    /*
     * Get a translation from site_generator locallang
     *
     * @param string $key The key from the LOCAL_LANG array for which to return the value.
     * @param array $arguments The arguments of the extension, being passed over to vsprintf
     * @param string $extensionName Extension name
     *
     * @return string|null The value from LOCAL_LANG or NULL if no translation was found.
     */
    public function translate($key, $arguments = null, $extensionName = 'site_generator'): ?string
    {
        return (LocalizationUtility::translate($key, $extensionName, $arguments));
    }

    /**
     * Get data from extension configuration, data can be override by site configuration
     *
     * @return array
     */
    public function getExtensionConfiguration(): array
    {
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator'];

        try {
            $request = $this->getRequest();
            $id = (int)($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);

            if ($id) {
                // Retrieve site generator configuration in site configuration
                $site = $this->siteFinder->getSiteByPageId($id);
                $siteConfiguration = $site->getConfiguration();

                // Override data with site configuration
                foreach ($siteConfiguration['siteGenerator'] as $key => $value) {
                    $extensionConfiguration[$key] = (string)$value;
                }
            }
        } catch (SiteNotFoundException $exception) {
            // No site configuration for this page
        }

        return ($extensionConfiguration);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
