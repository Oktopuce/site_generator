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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;

/**
 * StateUpdateSlugs
 */
class StateUpdateSlugs extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Update slugs for new tree structure
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Update slug
        $this->updateSlugs($context->getSiteData());
    }

    /**
     * Update slugs for new tree structure
     *
     * @param BaseDto $siteData New site data
     * @return void
     */
    protected function updateSlugs(BaseDto $siteData): void
    {
        // First flush all caches because slug parts are taken from 'cache_runtime'
        // but it's not possible to clear only 'cache_runtime' : no group associated to it
        /** @var CacheManager $runtimeCache */
        $runtimeCache = GeneralUtility::makeInstance(CacheManager::class);
        $runtimeCache->flushCaches();

        /** @var SlugHelper $slugHelper */
        $slugConf = $GLOBALS['TCA']['pages']['columns']['slug']['config'];
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, 'pages', 'slug', $slugConf);

        /* @var $pagesRepository PagesRepository */
        $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);

        foreach ($siteData->getMappingArrayMerge('pages') as $sitePid) {
            $origRow = BackendUtility::getRecord('pages', $sitePid);
            $slug = $slugHelper->generate($origRow, $origRow['pid']);

            $updateValues = ['slug' => $slug];

            $pagesRepository->updatePage($sitePid, $updateValues);
        }
        $this->log(LogLevel::INFO, 'Slugs updated for all pages');
    }

}
