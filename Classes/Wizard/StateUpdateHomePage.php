<?php

namespace Oktopuce\SiteGenerator\Wizard;

/***
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateUpdateHomePage
 */
class StateUpdateHomePage extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * Update home page with new name
     *
     * @param SiteGeneratorWizard $context
     * @return void
    */
    public function process(SiteGeneratorWizard $context)
    {
        $settings = $context->getSettings();

        // Update the home page with the form data
        $this->updateHomePage($context->getSiteData(), (bool)$settings['siteGenerator']['wizard']['hideHomePage']);
    }

    /**
     * Update the home page with the form data
     *
     * @param BaseDto $siteData New site data
     * @param bool $hideHomePage If true, home page will be hidden
     *
     * @return void
     */
    protected function updateHomePage(BaseDto $siteData, $hideHomePage)
    {
        $updateValues = [
            'title' => $siteData->getTitle(),
            'hidden' => $hideHomePage
        ];

        /* @var $pagesRepository PagesRepository */
        $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);
        $pagesRepository->updatePage($siteData->getHpPid(), $updateValues);

        $this->log(LogLevel::NOTICE, 'Update home page with form informations done');
        $siteData->addMessage($this->translate('generate.success.homePageUpdated', [$siteData->getTitle()]));
    }

}
