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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Context\Context;
use Oktopuce\SiteGenerator\Dto\BaseDto;

/**
 * SiteGeneratorWizard : based on design pattern 'State'
 */
class SiteGeneratorWizard
{

    /**
     * @var BaseDto
     */
    protected $siteData = null;

    /**
     * @var object
     */
    protected $currentState = null;

    /**
     * @var array States list from TS
     */
    private static $states = [];

    /**
     * Contains the settings of the current extension
     *
     * @var array
     */
    protected $settings;

    /**
     * Constructor of this class : set first wizard step and store site data from forms
     *
     * @param BaseDto $siteData Data coming from forms : mandatory and optional data
     * @return void
     */
    public function __construct(object $siteData)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'SiteGenerator');

        $this->siteData = $siteData;
        $this->getStates();
        $this->setNextWizardState();
    }

    /**
     *
     * Get extension setings from TS
     *
     * @return array
     */
    public function getSettings()
    {
        return ($this->settings);
    }

    /**
     * Get states from TS
     *
     * @return void
     */
    public function getStates()
    {
        ksort($this->settings['siteGenerator']['wizard']['steps']);
        self::$states = $this->settings['siteGenerator']['wizard']['steps'];
        reset(self::$states);
    }

    /**
     * Start site generation wizard
     *
     * @return void
     */
    public function startWizard()
    {
        // Force wizard with admin rights
        if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '>=')) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            $saveBeUserAdmin = $context->getPropertyFromAspect('backend.user', 'isAdmin');
        }
        else {
            $saveBeUserAdmin = $GLOBALS['BE_USER']->user['admin'];
        }

        // Don't know how to change it with aspect in Typo3 V9
        $GLOBALS['BE_USER']->user['admin'] = 1;

        // Process all steps : steps are defined in setup TS, field wizardSteps
        while ($this->currentState != NULL) {
            $this->currentState->process($this);
            $this->setNextWizardState();
        }

        // Restore Be User admin rights
        $GLOBALS['BE_USER']->user['admin'] = $saveBeUserAdmin;
    }

    /**
     * Set next wizard state
     *
     * @return void
     */
    public function setNextWizardState()
    {
        $this->currentState = null;

        if (self::$states) {
            $stateClass = current(self::$states);
            if ($stateClass !== false) {
                $this->currentState = GeneralUtility::makeInstance($stateClass);
                next(self::$states);
            }
        }
        else {
            throw new \Exception(LocalizationUtility::translate('wizard.tsConfig.error', 'site_generator'));
        }
    }

    /**
     * Get site data (i.e. data from Form/DTO)
     *
     * @return BaseDto
     */
    public function getSiteData()
    {
        return ($this->siteData);
    }

}
