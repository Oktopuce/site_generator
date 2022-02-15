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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Psr\Log\LogLevel;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateCopyModelSite
 */
class StateCopyModelSite extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * Copy model site
     *
     * @param SiteGeneratorWizard $context
     * @return void
     * @throws \Exception
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Copy the model site in current site
        $homePagePid = $this->copyModel($context->getSiteData());

        // Store home page pid in context
        $context->getSiteData()->setHpPid($homePagePid);
    }

    /**
     * Copy the model site in current page
     *
     * @param BaseDto $siteData New site data
     * @throws \Exception
     *
     * @return int The pid of the new Home Page
     */
    protected function copyModel(BaseDto $siteData): int
    {
        // Copy model site in current site root page
        $sitePage = $siteData->getPid();
        $modelPid = $siteData->getModelPid();
        $cmd = [];
        $cmd['pages'][$modelPid]['copy'] = $sitePage;

        /* @var $tce DataHandler */
        $tce = GeneralUtility::makeInstance(DataHandler::class);
        $tce->start([], $cmd);
        $tce->copyTree = 999;
        $tce->process_cmdmap();

        // Save mapping array merge in context : relation between original pid / new pid
        $siteData->setMappingArrayMerge($tce->copyMappingArray_merged);

        // Get the pid of home page copied
        $homePagePid = $tce->copyMappingArray_merged['pages'][$modelPid];

        if ($homePagePid > 0) {
            $this->log(LogLevel::NOTICE, 'Site model copy successful (pid home page = ' . $homePagePid . ')');
            // @extensionScannerIgnoreLine
            $siteData->addMessage($this->translate('generate.success.copySuccessfull', [$homePagePid]));
        }
        else {
            $this->log(LogLevel::ERROR, 'Site model copy error');
            throw new \Exception($this->translate('wizard.copyModel.error'));
        }

        return ($homePagePid);
    }

}
