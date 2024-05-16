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
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Utility\TemplateService;

/**
 * StateUpdateTemplate
 */
class StateUpdateTemplateHP extends StateBase implements SiteGeneratorStateInterface
{
    /**
     * @param TemplateService $templateService
     */
    public function __construct(
        readonly protected TemplateService          $templateService
    )
    {
        parent::__construct();
    }

    /**
     * Update site template with the new uids
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Update site template to set new uid
        $this->updateTemplate($context->getSiteData());
    }

    /**
     * Update site templates to set new uids
     *
     * @param BaseDto $siteData New site data
     * @return void
     */
    protected function updateTemplate(BaseDto $siteData): void
    {
        // Cf. app/vendor/typo3/cms-tstemplate/Classes/Controller/ConstantEditorController.php
        $allTemplatesOnPage = $this->templateService->getAllTemplateRecordsOnPage($siteData->getHpPid());
        foreach ($allTemplatesOnPage as $template) {
            if($this->templateService->updateContent('sys_template', $template, 'constants', $siteData)) {
                // @extensionScannerIgnoreLine
                $siteData->addMessage($this->translate('generate.success.templateHpUpdated'));
                $this->log(LogLevel::NOTICE, 'Update home page template with new uids done');
            };
        }
    }
}