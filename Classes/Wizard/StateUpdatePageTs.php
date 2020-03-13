<?php

namespace Oktopuce\SiteGenerator\Wizard;

/* * *
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * * */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateUpdateTemplate
 */
class StateUpdatePageTs extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Update Page TS with the new uids (i.e. : for TCEMAIN.clearCacheCmd)
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context)
    {
        // Update site template to set new uid
        $this->updatePageTS($context->getSiteData());
    }

    /**
     * Update page TS to set new uids for TCEMAIN.clearCacheCmd
     *
     * @param BaseDto $siteData New site data
     * @return void
     */
    protected function updatePageTS(BaseDto $siteData)
    {
        $pagesUpdated = [];

        // Iterates over new pages
        foreach ($siteData->getMappingArrayMerge() as $pageId) {
            $update = false;

            // Get record data
            $origRow = BackendUtility::getRecord('pages', $pageId);

            $newsTSConfig = '';
            $line = strtok($origRow['TSconfig'], PHP_EOL);
            $matches = null;

            while ($line !== false) {
                // If clearCacheCmd found, update it with new uids
                if (preg_match('/(clearCacheCmd)(\s)*=(.*)/', $line, $matches)) {
                    $commands = GeneralUtility::trimExplode(',', $matches[3], true);
                    $clearCacheCommands = array_unique($commands);

                    foreach ($clearCacheCommands as &$clearCacheCommand) {
                        $clearCacheCommand = ($siteData->getMappingArrayMerge()[$clearCacheCommand] ?? '');
                    }

                    $newsTSConfig .= str_replace($matches[0], $matches[1] . ' = ' . implode(',', $clearCacheCommands), $line) . PHP_EOL;
                    $update = true;
                }
                else {
                    $newsTSConfig .= $line . PHP_EOL;
                }
                $line = strtok(PHP_EOL);
            }

            if ($update) {
                // Set the data to be saved
                $recData = [];
                $recData['pages'][$pageId]['TSconfig'] = $newsTSConfig;
                // Create new  tce-object
                $tce = GeneralUtility::makeInstance(DataHandler::class);
                $tce->start($recData, []);
                $tce->process_datamap();

                $pagesUpdated[] = $pageId;
            }
        }
        if (!empty($pagesUpdated)) {
            $this->log(LogLevel::NOTICE, 'Update page TSConfig : TCEMAIN.clearCacheCmd done for pages : ' . implode(',', $pagesUpdated));
            $siteData->addMessage($this->translate('generate.success.updatePageTS', [implode(',', $pagesUpdated)]));
        }
    }

}
