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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Utility\ExtendedTemplateService;

/**
 * StateUpdateTemplate
 */
class StateUpdateTemplateHP extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Update site template with the new uids
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Update site template to set new uid
        $this->updateTemplate($context->getSiteData());
    }

    /**
     * Update site template to set new uids
     *
     * @param BaseDto $siteData New site data
     * @return void
     */
    protected function updateTemplate(BaseDto $siteData): void
    {
        /** @var ExtendedTemplateService $templateService */
        $templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);
        // Get the row of the first VISIBLE template of the page. where clause like the frontend.
        $templateRow = $templateService->ext_getFirstTemplate($siteData->getHpPid());

        if (!empty($templateRow)) {
            $templateService->ext_regObjectPositions($templateRow['constants']);
            $objReg = $templateService->getObjReg();

            foreach ($objReg as $key => $rawP) {
                $value = GeneralUtility::trimExplode('=', $templateService->raw[$rawP]);
                foreach ($siteData->getMappingArrayMerge() as $modelPid => $sitePid) {
                    if ($modelPid === (int)$value[1]) {
                        $templateService->ext_putValueInConf($key, $sitePid);
                    }
                }
            }

            if ($templateService->changed) {
                // Set the data to be saved
                $recData = [];
                $saveId = $templateRow['uid'];
                $recData['sys_template'][$saveId]['constants'] = implode(LF, $templateService->raw);
                // Create new  tce-object
                $tce = GeneralUtility::makeInstance(DataHandler::class);
                $tce->start($recData, []);
                $tce->process_datamap();

                $this->log(LogLevel::NOTICE, 'Update home page template with new pid done');
            }
        }
    }

}
