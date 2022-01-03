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

use Oktopuce\SiteGenerator\Dto\BaseDto;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Base class for all states : logger, extension configuration, ...
 */
class StateBase
{
    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger = null;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /*
     * Adds a log record
     *
     * @param int|string $level Log level. Value according to \TYPO3\CMS\Core\Log\LogLevel. Alternatively accepts a string.
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @return void
     */
    public function log($level, $message, array $data = []): void
    {
        $this->logger->log($level, "Site generator : " . $message, $data);
    }

    /*
     * Get a translation from site_generator locallang
     *
     * @param string $key The key from the LOCAL_LANG array for which to return the value.
     * @param array $arguments The arguments of the extension, being passed over to vsprintf
     * @param string $extensionName Extension name
     *
     * @return string|null The value from LOCAL_LANG or NULL if no translation was found.
     */
    public function translate($key, $arguments = null, $extensionName = 'site_generator'): ?string
    {
        return (LocalizationUtility::translate($key, $extensionName, $arguments));
    }

    /**
     * Get data from extension configuration
     *
     * @return array
     */
    public function getExtensionConfiguration(): array
    {
        return ($this->extensionConfiguration == null ? $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator'] : []);
    }

    public function getSiteFolderName(): string
    {
        return $this->getExtensionConfiguration()['siteFolderName'] ?? 'siteTitle';
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    protected function getStorageUidFromGroupHomePath() {
        return $this->getGroupHomePathArray()[0];
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    protected function getFolderFromGroupHomePath() {
        return trim($this->getGroupHomePathArray()[1], '/');
    }

    /**
     * @return false|string[]
     * @throws \Exception
     */
    protected function getGroupHomePathArray() {
        $groupHomePathArray = explode(':', $GLOBALS['TYPO3_CONF_VARS']['BE']['groupHomePath']);
        if (count($groupHomePathArray) === 2 && is_numeric($groupHomePathArray[0])) {
            return $groupHomePathArray;
        } else {
            throw new \Exception('The Installation-Wide Option [BE][groupHomePath] was not configured correctly. Should be a combined folder identifier. Eg. 2:groups/');
        }
    }

    /**
     * @param $siteData
     * @return string
     * @throws \Exception
     */
    public function getSiteFolder($siteData) {
        if($this->getSiteFolderName() === 'userGroupUid') {
            if($siteData->getBeGroupId()) {
                return (string) $siteData->getBeGroupId();
            } else {
                throw new \Exception('The extension configuration siteFolderName was set to userGroupUid, but the usergroup uid was not found. Please check order of the states. StateCreateBeGroup should come before StateCreateGroupHomeFolder.');
            }
        } else {
            return strtolower($siteData->getTitleSanitize());
        }
    }

    public function getBaseFolderName(BaseDto $siteData) {
        if($this->getSiteFolderName() === 'userGroupUid') {
            return $this->getFolderFromGroupHomePath();
        } else {
            return $siteData->getBaseFolderName();
        }
    }

    /**
     * @param SiteGeneratorWizard $context
     * @return int|void
     * @throws \Exception
     */
    public function getStorageUid(SiteGeneratorWizard $context) {
        if($this->getSiteFolderName() === 'userGroupUid') {
            return $this->getStorageUidFromGroupHomePath();
        } else {
            $settings = $context->getSettings();
            return (int)$settings['siteGenerator']['wizard']['storageUid'];
        }
    }

    /**
     * @param SiteGeneratorWizard $context
     * @return string
     * @throws \Exception
     */
    public function getSiteFolderCombinedIdentifier(SiteGeneratorWizard $context) {
        $siteData = $context->getSiteData();
        return $this->getStorageUid($context) . ':' . $this->getBaseFolderName($siteData) . '/' . $this->getSiteFolder($siteData) . '/';
    }
}
