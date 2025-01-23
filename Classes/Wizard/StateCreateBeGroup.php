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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * StateCreateBeGroup
 */
class StateCreateBeGroup extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(readonly protected DataHandler $dataHandler)
    {
        parent::__construct();
    }

    /**
     * Create BE user group
     *
     * @param SiteGeneratorWizard $context
     * @return void
     * @throws \Exception
     */
    public function process(SiteGeneratorWizard $context): void
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
        $newUniqueId = StringUtility::getUniqueId('NEW');
        $groupName = ($siteData->getGroupPrefix() ? $siteData->getGroupPrefix() . ' - ' : '') . $siteData->getTitle();
        $data['be_groups'][$newUniqueId] = [
            'pid' => 0,
            'title' => $groupName,
            'db_mountpoints' => $siteData->getHpPid(),
            'groupMods' => ($extensionConfiguration['groupMods'] ?: null),
            'tables_select' => ($extensionConfiguration['tablesSelect'] ?: null),
            'tables_modify' => ($extensionConfiguration['tablesModify'] ?: null),
            'explicit_allowdeny' => ($extensionConfiguration['explicitAllowdeny'] ?: null),
            'TSconfig' => 'options.defaultUploadFolder = 1:' . ($siteData->getBaseFolderName() ? $siteData->getBaseFolderName() . '/' : '') . strtolower($siteData->getTitleSanitize()) . '/',
            'file_mountpoints' => ''
        ];

        // Set common mountpoint
        if ($siteData->getCommonMountPointUid()) {
            $data['be_groups'][$newUniqueId]['file_mountpoints'] = $siteData->getCommonMountPointUid();
        }

        // Set created mountpoint
        if ($siteData->getMountId()) {
            $data['be_groups'][$newUniqueId]['file_mountpoints'] .= ($siteData->getCommonMountPointUid() ? ',' : '') . $siteData->getMountId();
        }

        $this->dataHandler->start($data, []);
        $this->dataHandler->process_datamap();

        // Retrieve uid of user group created
        $groupId = $this->dataHandler->substNEWwithIDs[$newUniqueId] ?? 0;

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
