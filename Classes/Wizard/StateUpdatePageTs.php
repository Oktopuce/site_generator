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
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateUpdateTemplate.
 */
class StateUpdatePageTs extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(readonly protected DataHandler $dataHandler)
    {
        parent::__construct();
    }

    /**
     * Update Page TS with the new uids (i.e. : for TCEMAIN.clearCacheCmd).
     *
     * @param SiteGeneratorWizard $context
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Update site template to set new uid
        $this->updatePageTS($context->getSiteData());
    }

    /**
     * Update page TS to set new uids for TCEMAIN.clearCacheCmd.
     *
     * @param BaseDto $siteData New site data
     */
    protected function updatePageTS(BaseDto $siteData): void
    {
        $pagesUpdated = [];

        // Iterates over new pages
        foreach ($siteData->getMappingArrayMerge('pages') as $pageId) {
            $update = false;

            // Get record data
            $origRow = BackendUtility::getRecord('pages', $pageId);

            if ($origRow['TSconfig']) {
                $newsTSConfig = '';
                $line = strtok($origRow['TSconfig'], PHP_EOL);
                $matches = null;

                while ($line !== false) {
                    // If clearCacheCmd found, update it with new uids
                    if (preg_match('/(clearCacheCmd)(\s)*=(.*)/', $line, $matches)) {
                        $commands = GeneralUtility::trimExplode(',', $matches[3], true);
                        $clearCacheCommands = array_unique($commands);

                        $clearCacheCommandNew = [];
                        foreach ($clearCacheCommands as $clearCacheCommand) {
                            $clearCacheCommandNew[] = ($siteData->getMappingArrayMerge('pages')[$clearCacheCommand] ?? '');
                        }

                        $newsTSConfig .= str_replace($matches[0], $matches[1] . ' = ' . implode(',', $clearCacheCommandNew), $line) . PHP_EOL;
                        $update = true;
                    } else {
                        $newsTSConfig .= $line . PHP_EOL;
                    }
                    $line = strtok(PHP_EOL);
                }

                if ($update) {
                    // Set the data to be saved
                    $recData = [];
                    $recData['pages'][$pageId]['TSconfig'] = $newsTSConfig;
                    // Create new  tce-object
                    $this->dataHandler->start($recData, []);
                    $this->dataHandler->process_datamap();

                    $pagesUpdated[] = $pageId;
                }
            }
        }

        if (!empty($pagesUpdated)) {
            $this->log(LogLevel::NOTICE, 'Update page TSConfig : TCEMAIN.clearCacheCmd done for pages : ' . implode(',', $pagesUpdated));
            // @extensionScannerIgnoreLine
            $siteData->addMessage($this->translate('generate.success.updatePageTS', [implode(',', $pagesUpdated)]));
        }
    }
}
