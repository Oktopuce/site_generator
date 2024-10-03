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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Psr\Log\LogLevel;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use TYPO3\CMS\Backend\Exception\SiteValidationErrorException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * StateSiteConfiguration
 */
class StateSiteConfiguration extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(
        readonly protected PagesRepository $pagesRepository,
        readonly protected DataHandler $dataHandler
    )
    {
        parent::__construct();
    }

    /**
     * Create site management
     *
     * @param SiteGeneratorWizard $context
     * @return void
     * @throws Exception
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Create the domain name on first page
        $this->createSiteConfiguration($context->getSiteData());
    }

    /**
     * Create a site configuration
     *
     * @param BaseDto $siteData New site data
     * @return void
     * @throws \Exception|Exception
     */
    protected function createSiteConfiguration(BaseDto $siteData): void
    {
        if (!empty($siteData->getDomain())) {
            $uids = $siteData->getMappingArrayMerge('pages');
            $rootSiteId = $this->pagesRepository->getRootSiteId($uids);

            if ($rootSiteId) {
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

                    $siteIdentifier = $extensionConfiguration['siteIdentifierPrefix'] . md5((string)$rootSiteId);

                    $typo3VersionNumber = VersionNumberUtility::getNumericTypo3Version();

                    /** @var \TYPO3\CMS\Core\Configuration\SiteWriter $siteConfiguration */
                    $siteConfiguration = GeneralUtility::makeInstance(
                    version_compare($typo3VersionNumber, '13.0.0', '<')
                        ? \TYPO3\CMS\Core\Configuration\SiteConfiguration::class
                        : \TYPO3\CMS\Core\Configuration\SiteWriter::class);

                    // Persist the configuration
                    $siteConfiguration->write($siteIdentifier, $newSiteConfiguration);

                    // Update slugs
                    $this->updateSlugForPage($rootSiteId);

                    $this->log(LogLevel::NOTICE, 'Site configuration created');
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.createSiteConfiguration', [
                        $siteData->getDomain()
                    ]));
                } catch (SiteValidationErrorException) {
                    $this->log(LogLevel::ERROR,
                        'Cannot create site configuration for domain : ' . $siteData->getDomain());
                    throw new SiteValidationErrorException($this->translate('wizard.createSiteConfiguration.error'));
                }
            } else {
                $this->log(LogLevel::WARNING,
                    'The selected model does not contains root pages, no site configuration created');
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
