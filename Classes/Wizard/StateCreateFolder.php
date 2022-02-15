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
use Psr\Log\LogLevel;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * StateCreateFolder
 */
class StateCreateFolder extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * @var ResourceFactory
     */
    private ResourceFactory$resourceFactory;

    /**
     * @param ResourceFactory $resourceFactory
     */
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
        $settings = $context->getSettings();

        // Create folders in storage
        $this->createFolders($context->getSiteData(), (int)$settings['siteGenerator']['wizard']['storageUid']);
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
                    if (!empty($baseFolderName) && $baseFolder !== null) {
                        // @extensionScannerIgnoreLine
                        $siteData->addMessage($this->translate('generate.success.folderExist', [$currentFolder]));
                    }
                }

                // Create site folder from site title
                $newFolder = strtolower($siteData->getTitleSanitize());
                $currentFolder .= '/' . $newFolder;
                if (!$storage->hasFolderInFolder($newFolder, $baseFolder)) {
                    // Create sub-folder for current site
                    $siteFolder = $storage->createFolder($newFolder, $baseFolder);
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.folderCreated', [$currentFolder]));
                } else {
                    $siteFolder = $storage->getFolderInFolder($newFolder, $baseFolder);
                    // @extensionScannerIgnoreLine
                    $siteData->addMessage($this->translate('generate.success.folderExist', [$currentFolder]));
                }

                // Create all sub folders if they don't exist
                $baseSubFolder = $currentFolder;
                foreach ($subFolderNames as $subFolderName) {
                    $currentFolder = $baseSubFolder . '/' . $subFolderName;
                    if (!$storage->hasFolderInFolder($subFolderName, $siteFolder)) {
                        $storage->createFolder($subFolderName, $siteFolder);
                        $siteData->addMessage($this->translate('generate.success.folderCreated', [$currentFolder]));
                    }
                }
            } catch (InsufficientFolderWritePermissionsException $e) {
                $this->log(LogLevel::ERROR, 'You are not allowed to create directories! ("%s")', [$currentFolder]);
                throw new \RuntimeException($this->translate('wizard.folderCreation.error', [$currentFolder]));
            } catch (InsufficientFolderAccessPermissionsException $e) {
                $this->log(LogLevel::ERROR, 'You don\'t have full access to the destination directory "%s"!', [$currentFolder]);
                throw new \RuntimeException($this->translate('wizard.folderCreation.error', [$currentFolder]));
            }

            $this->log(LogLevel::NOTICE, 'Folder creation successfull');
        }
    }
}
