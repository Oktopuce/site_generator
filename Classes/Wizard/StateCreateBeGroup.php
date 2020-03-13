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
    public function process(SiteGeneratorWizard $context)
    {
        // Create BE group
        $groupId = $this->createBeGroup($context->getSiteData());

        $context->getSiteData()->setBeGroupId($groupId);
    }

    /**
     * Create BE group with file mount, DB mount, access lists
     *
     * @param BaseDto $siteData New site data
     * @throws \Exception
     * @return int The uid of the group created
     */
    protected function createBeGroup(BaseDto $siteData): int
    {
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
            'TSconfig' => 'options.defaultUploadFolder = 1:' . ($siteData->getBaseFolderName() ? $siteData->getBaseFolderName() . '/' : '') . strtolower($siteData->getTitleSanitize()) . '/'
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

        if ($groupId > 0) {
            $this->log(LogLevel::NOTICE, 'Create BE group successful (uid = ' . $groupId);
            $siteData->addMessage($this->translate('generate.success.beGroupCreated', [$groupName, $groupId]));
        }
        else {
            $this->log(LogLevel::ERROR, 'Create BE group error');
            throw new \Exception($this->translate('wizard.beGroup.error'));
        }

        return ($groupId);
    }

}
