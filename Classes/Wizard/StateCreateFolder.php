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

use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Log\LogLevel;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateCreateFolder
 */
class StateCreateFolder extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    private string $folderName;

    public function __construct(ResourceFactory $resourceFactory)
    {
        parent::__construct();
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * Create site folder in fileadmin : base Folder / site title / sub folder
     *
     * @param SiteGeneratorWizard $context
     * @return void
     * @throws \Exception
     */
    public function process(SiteGeneratorWizard $context): void
    {
        $siteData = $context->getSiteData();
        $settings = $context->getSettings();
        $storageUid = (int)$settings['siteGenerator']['wizard']['storageUid'];
        if(get_class($this) == 'Oktopuce\SiteGenerator\Wizard\StateCreateGroupHomeFolder' && $this->getSiteFolderName() == 'userGroupUid' ) {
            $groupHomePathData = explode(':', $GLOBALS['TYPO3_CONF_VARS']['BE']['groupHomePath']);
            if (count($groupHomePathData) === 2 && is_numeric($groupHomePathData[0])) {
                $storageUid = $groupHomePathData[0];
                $siteData->setBaseFolderName(trim($groupHomePathData[1], '/'));
            } else {
                throw new \Exception('The extension configuration siteFolderName was set to userGroupUid, but the Installation-Wide Option [BE][groupHomePath] was not configured correctly. Should be a combined folder identifier. Eg. 2:groups/');
            }
            if ($siteData->getBeGroupId()) {
                $this->folderName = (string)$siteData->getBeGroupId();
            } else {
                throw new \Exception('The extension configuration siteFolderName was set to userGroupUid, but the usergroup uid was not found. Please check order of the states. StateCreateBeGroup should come before StateCreateGroupHomeFolder.');
            }
        } else if(get_class($this) == 'Oktopuce\SiteGenerator\Wizard\StateCreateFolder' && $this->getSiteFolderName() == 'siteTitle') {
            $this->folderName = strtolower($siteData->getTitleSanitize());
        }
        if(isset($this->folderName)) {
            $this->createFolders($siteData, (int)$storageUid);
        }
    }

    /**
     * Create folder "fileadmin/base_folder/sites_title", with sub-folders "documents" and "images"
     *
     * @param BaseDto $siteData New site data
     * @param int $storageUid The uid of storage to use
     * @throws \Exception
     *
     * @return void
     */
    protected function createFolders(BaseDto $siteData, int $storageUid): void
    {
        // Get base folder and sub-folders name to create
        $baseFolderName = $siteData->getBaseFolderName();
        $subFolderNames = GeneralUtility::trimExplode(',', $siteData->getSubFolderNames(), true);

        if ($storageUid) {
            $storage = $this->resourceFactory->getStorageObject($storageUid);

            try {
                $currentFolder = $baseFolderName;
                if (!$storage->hasFolder($currentFolder)) {
                    // Folder does not exists : create it
                    $baseFolder = $storage->createFolder($baseFolderName);
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.folderCreated', [$currentFolder]));
                } else {
                    $baseFolder = $storage->getFolder($baseFolderName);
                    if (!empty($baseFolderName) && !empty($baseFolder)) {
                        // @extensionScannerIgnoreLine
                        $siteData->addMessage($this->translate('generate.success.folderExist', [$currentFolder]));
                    }
                }

                // Create site folder
                $currentFolder .= '/' . $this->folderName;
                if (!$storage->hasFolderInFolder($this->folderName, $baseFolder)) {
                    // Create sub-folder for current site
                    $siteFolder = $storage->createFolder($this->folderName, $baseFolder);
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.folderCreated', [$currentFolder]));
                } else {
                    $siteFolder = $storage->getFolderInFolder($this->folderName, $baseFolder);
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.folderExist', [$currentFolder]));
                }

                // Create all sub folders if they don't exist
                $baseSubFolder = $currentFolder;
                foreach ($subFolderNames as $subFolderName) {
                    $currentFolder = $baseSubFolder . '/' . $subFolderName;
                    if (!$storage->hasFolderInFolder($subFolderName, $siteFolder)) {
                        $subFolder = $storage->createFolder($subFolderName, $siteFolder);
                        $siteData->addMessage($this->translate('generate.success.folderCreated', [$currentFolder]));
                    }
                }
            } catch (InsufficientFolderAccessPermissionsException $e) {
                $this->log(LogLevel::ERROR, 'You don\'t have full access to the destination directory "%s"!', [$currentFolder]);
                throw new \Exception($this->translate('wizard.folderCreation.error', [$currentFolder]));
            } catch (InsufficientFolderWritePermissionsException $e) {
                $this->log(LogLevel::ERROR, 'You are not allowed to create directories! ("%s")', [$currentFolder]);
                throw new \Exception($this->translate('wizard.folderCreation.error', [$currentFolder]));
            }

            $this->log(LogLevel::NOTICE, 'Folder creation successfull');
        }
    }
}
