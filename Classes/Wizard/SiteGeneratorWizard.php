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

use Oktopuce\SiteGenerator\Exception\TsConfigException;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Context\Context;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Exception;

/**
 * SiteGeneratorWizard : based on design pattern 'State'.
 */
class SiteGeneratorWizard
{
    protected BaseDto $siteData;

    protected ?object $currentState = null;

    /**
     * @var array States list from TS
     */
    private static array $states = [];

    /**
     * Contains the settings of the current extension.
     */
    protected array $settings = [];

    /**
     * Get extension settings from TypoScript.
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Get states from TS.
     */
    protected function getStates(): void
    {
        ksort($this->settings['siteGenerator']['wizard']['steps']);
        self::$states = $this->settings['siteGenerator']['wizard']['steps'];
        reset(self::$states);
    }

    /**
     * Start site generation wizard.
     *
     * @param BaseDto $siteData Data coming from forms : mandatory and optional data
     * @param array   $settings Settings from TypoScript configuration (could have been override with Page TsConfig)
     *
     * @throws AspectNotFoundException
     * @throws Exception
     */
    public function startWizard(BaseDto $siteData, array $settings): void
    {
        $this->settings = $settings;

        $this->getStates();
        $this->setNextWizardState();

        // Set data coming from form
        $this->siteData = $siteData;

        // Force wizard with admin rights
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $saveBeUserAdmin = $context->getPropertyFromAspect('backend.user', 'isAdmin');

        // Don't know how to change it with aspect in Typo3 V9
        $GLOBALS['BE_USER']->user['admin'] = true;

        // Process all steps : steps are defined in setup TS, field wizardSteps
        while ($this->currentState !== null) {
            $this->currentState->process($this);
            $this->setNextWizardState();
        }

        // Restore Be User admin rights
        $GLOBALS['BE_USER']->user['admin'] = $saveBeUserAdmin;
    }

    /**
     * Set next wizard state.
     *
     * @throws Exception
     */
    public function setNextWizardState(): void
    {
        $this->currentState = null;

        if (self::$states) {
            $stateClass = current(self::$states);
            if ($stateClass !== false) {
                $this->currentState = GeneralUtility::makeInstance($stateClass);
                next(self::$states);
            }
        } else {
            throw new TsConfigException(LocalizationUtility::translate('wizard.tsConfig.error', 'SiteGenerator'), 5866979200);
        }
    }

    /**
     * Get site data (i.e. data from Form/DTO).
     */
    public function getSiteData(): BaseDto
    {
        return $this->siteData;
    }
}
