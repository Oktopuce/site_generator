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

use Psr\Log\LogLevel;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateUpdateHomePage.
 */
class StateUpdateHomePage extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(readonly protected PagesRepository $pagesRepository)
    {
        parent::__construct();
    }

    /**
     * Update home page with new name.
     */
    public function process(SiteGeneratorWizard $context): void
    {
        $settings = $context->getSettings();

        // Update the home page with the form data
        $this->updateHomePage($context->getSiteData(), (int) ($settings['siteGenerator']['wizard']['hideHomePage'] ?? 0));
    }

    /**
     * Update the home page with the form data.
     *
     * @param BaseDto $siteData     New site data
     * @param int     $hideHomePage If 1, home page will be hidden
     */
    protected function updateHomePage(BaseDto $siteData, int $hideHomePage): void
    {
        $updateValues = [
            'title' => $siteData->getTitle(),
            'hidden' => $hideHomePage,
        ];

        $this->pagesRepository->updatePage($siteData->getHpPid(), $updateValues);

        $this->log(LogLevel::NOTICE, 'Update home page with form informations done');
        // @extensionScannerIgnoreLine
        $siteData->addMessage($this->translate('generate.success.homePageUpdated', [$siteData->getTitle()]));
    }
}
