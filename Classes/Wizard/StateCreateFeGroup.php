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
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateCreateFeGroup
 */
class StateCreateFeGroup extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Create FE user group
     *
     * @param SiteGeneratorWizard $context
     * @return void
     * @throws \Exception
     */
    public function process(SiteGeneratorWizard $context): void
    {
        $settings = $context->getSettings();

        // Create FE group
        $groupId = $this->createFeGroup($context->getSiteData(), (int)$settings['siteGenerator']['wizard']['pidFeGroup']);
        $context->getSiteData()->setFeGroupId($groupId);
    }

    /**
     * Create FE group
     *
     * @param BaseDto $siteData New site data
     * @param int $pidFeGroup Pid for FE group creation
     * @throws \Exception
     *
     * @return int The uid of the group created
     */
    protected function createFeGroup(BaseDto $siteData, int $pidFeGroup): int
    {
        $groupId = 0;

        if ($pidFeGroup) {
            // Create a new FE group
            $data = [];
            $newUniqueId = 'NEW' . uniqid();
            $groupName = ($siteData->getGroupPrefix() ? $siteData->getGroupPrefix() . ' - ' : '') . $siteData->getTitle();
            $data['fe_groups'][$newUniqueId] = [
                'pid' => $pidFeGroup,
                'title' => $groupName
            ];

            /* @var $tce DataHandler */
            $tce = GeneralUtility::makeInstance(DataHandler::class);
            $tce->stripslashes_values = 0;
            $tce->start($data, []);
            $tce->process_datamap();

            // Retrieve uid of user group created
            $groupId = $tce->substNEWwithIDs[$newUniqueId];

            if ($groupId > 0) {
                $this->log(LogLevel::NOTICE, 'Create FE group successful (uid = ' . $groupId);
                // @extensionScannerIgnoreLine
                $siteData->addMessage($this->translate('generate.success.feGroupCreated', [$groupName, $groupId]));
            } else {
                $this->log(LogLevel::ERROR, 'Create FE group error, check the value of module.tx_sitegenerator.settings.wizard.pidFeGroup');
                throw new \UnexpectedValueException($this->translate('wizard.feGroup.error'));
            }
        } else {
            $this->log(LogLevel::WARNING, "FE Group couldn't be created because module.tx_sitegenerator.settings.wizard.pidFeGroup is not defined");
            // @extensionScannerIgnoreLine
            $siteData->addMessage($this->translate('generate.success.noFeGroupCreated'));
        }

        return ($groupId);
    }
}
