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

/**
 * StateCreateBeGroup
 */
class StateCreateBeGroup extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * Create BE user group
     *
     * @param SiteGeneratorWizard $context
     * @return void
    */
    public function process(SiteGeneratorWizard $context): void
    {
        // Create BE group
        $groupId = $this->createBeGroup($context);
    }

    /**
     * Create BE group with file mount, DB mount, access lists
     *
     * @param SiteGeneratorWizard $context
     * @throws \Exception
     * @return int The uid of the group created
     */
    protected function createBeGroup(SiteGeneratorWizard $context): int
    {
        // New site data
        $siteData = $context->getSiteData();

        // Get extension configuration
        $extensionConfiguration = $this->getExtensionConfiguration();

        // Create a new group with filemount at root page
        $data = [];
        $newUniqueId = 'NEW' . uniqid();
        $groupName = ($siteData->getGroupPrefix() ? $siteData->getGroupPrefix() . ' - ' : '') . $siteData->getTitle();
        $data['be_groups'][$newUniqueId] = [
            'pid' => 0,
            'title' => $groupName,
            'db_mountpoints' => $siteData->getHpPid(),
            'groupMods' => ($extensionConfiguration['groupMods'] ?: null),
            'tables_select' => ($extensionConfiguration['tablesSelect'] ?: null),
            'tables_modify' => ($extensionConfiguration['tablesModify'] ?: null),
            'explicit_allowdeny' => ($extensionConfiguration['explicitAllowdeny'] ?: null),
//            'TSconfig' => 'options.defaultUploadFolder = 1:' . ($siteData->getBaseFolderName() ? $siteData->getBaseFolderName() . '/' : '') . strtolower($siteData->getTitleSanitize()) . '/'
        ];

        // Set common mountpoint
        if ($siteData->getCommonMountPointUid()) {
            $data['be_groups'][$newUniqueId]['file_mountpoints'] = $siteData->getCommonMountPointUid();
        }

        // Set created mountpoint
        if ($siteData->getMountId()) {
            $data['be_groups'][$newUniqueId]['file_mountpoints'] .= ($siteData->getCommonMountPointUid() ? ',' : '') . $siteData->getMountId();
        }

        /* @var $tce DataHandler */
        $tce = GeneralUtility::makeInstance(DataHandler::class);
        $tce->stripslashes_values = 0;
        $tce->start($data, []);
        $tce->process_datamap();

        // Retrieve uid of user group created
        $groupId = $tce->substNEWwithIDs[$newUniqueId];

        // Update the TSconfig field
        $context->getSiteData()->setBeGroupId($groupId);
        unset($data);
        $data['be_groups'][$groupId] = [
            'TSconfig' => 'options.defaultUploadFolder = ' . $this->getSiteFolderCombinedIdentifier($context)
        ];
        $tce->start($data, []);
        $tce->process_datamap();


        if ($groupId > 0) {
            $this->log(LogLevel::NOTICE, 'Create BE group successful (uid = ' . $groupId);
            // @extensionScannerIgnoreLine
            $siteData->addMessage($this->translate('generate.success.beGroupCreated', [$groupName, $groupId]));
        }
        else {
            $this->log(LogLevel::ERROR, 'Create BE group error');
            throw new \Exception($this->translate('wizard.beGroup.error'));
        }

        return ($groupId);
    }

}
