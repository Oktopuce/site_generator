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

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Psr\Log\LogLevel;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use TYPO3\CMS\Backend\Exception\SiteValidationErrorException;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * StateSiteConfiguration.
 */
class StateSiteConfiguration extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(
        readonly protected PagesRepository $pagesRepository,
        readonly protected DataHandler $dataHandler
    ) {
        parent::__construct();
    }

    /**
     * Create site management.
     *
     * @throws Exception
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Create the domain name on first page
        $this->createSiteConfiguration($context->getSiteData());
    }

    /**
     * Create a site configuration.
     *
     * @param BaseDto $siteData New site data
     *
     * @throws \Exception|Exception
     */
    protected function createSiteConfiguration(BaseDto $siteData): void
    {
        if (!empty($siteData->getDomain())) {
            $uids = $siteData->getMappingArrayMerge('pages');
            $rootSiteId = $this->pagesRepository->getRootSiteId($uids);

            if ($rootSiteId !== 0) {
                try {
                    // Get extension configuration
                    $extensionConfiguration = $this->getExtensionConfiguration();

                    $language = [];
                    $language['title'] = $extensionConfiguration['langTitle'];
                    $language['enabled'] = true;
                    $language['base'] = '/';
                    $language['typo3Language'] = 'default';
                    $language['locale'] = $extensionConfiguration['locale'];
                    $language['iso-639-1'] = $extensionConfiguration['iso-639-1'];
                    $language['websiteTitle'] = '';
                    $language['navigationTitle'] = $extensionConfiguration['navigationTitle'];
                    $language['hreflang'] = $extensionConfiguration['hreflang'];
                    $language['direction'] = $extensionConfiguration['direction'];
                    $language['flag'] = $extensionConfiguration['flag'];
                    $language['languageId'] = '0';

                    $newSiteConfiguration = [];
                    $newSiteConfiguration['rootPageId'] = $rootSiteId;
                    $newSiteConfiguration['websiteTitle'] = $siteData->getTitleSanitize();
                    $newSiteConfiguration['base'] = $siteData->getDomain();
                    $newSiteConfiguration['baseVariants'] = [];
                    $newSiteConfiguration['languages']['0'] = $language;

                    $siteIdentifier = $extensionConfiguration['siteIdentifierPrefix'] . md5((string) $rootSiteId);

                    if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() <= 12) {
                        $siteConfiguration = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\SiteConfiguration::class);

                        // Persist the configuration
                        $siteConfiguration->write($siteIdentifier, $newSiteConfiguration);
                    } else {
                        $siteConfiguration = GeneralUtility::makeInstance(SiteWriter::class);

                        // Persist the configuration
                        $siteConfiguration->createNewBasicSite($siteIdentifier, $newSiteConfiguration['rootPageId'], $newSiteConfiguration['base']);
                        $siteConfiguration->writeSettings($siteIdentifier, $newSiteConfiguration);
                    }

                    // Update slugs
                    $this->updateSlugForPage($rootSiteId);

                    $this->log(LogLevel::NOTICE, 'Site configuration created');
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.createSiteConfiguration', [
                        $siteData->getDomain(),
                    ]));
                } catch (SiteValidationErrorException) {
                    $this->log(
                        LogLevel::ERROR,
                        'Cannot create site configuration for domain : ' . $siteData->getDomain()
                    );
                    throw new SiteValidationErrorException($this->translate('wizard.createSiteConfiguration.error'), 5937698518);
                }
            } else {
                $this->log(
                    LogLevel::WARNING,
                    'The selected model does not contains root pages, no site configuration created'
                );
                // @extensionScannerIgnoreLine
                $siteData->addMessage($this->translate('wizard.createSiteConfiguration.error.noRooTPage'));
            }
        }
    }

    /**
     * Updates the slug of the given pageId by spinning up a new DataHandler instance.
     */
    protected function updateSlugForPage(int $pageId): void
    {
        $dataMap = [
            'pages' => [
                $pageId => [
                    'slug' => '',
                ],
            ],
        ];
        $this->dataHandler->start($dataMap, []);
        $this->dataHandler->process_datamap();
    }
}
