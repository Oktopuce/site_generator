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

use Doctrine\DBAL\Exception;
use Oktopuce\SiteGenerator\Utility\TemplateDirectivesService;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Utility\TemplateService;
use Oktopuce\SiteGenerator\Wizard\Event\UpdateTemplateHPEvent;

/**
 * StateUpdateTemplate.
 */
class StateUpdateTemplateHP extends StateBase implements SiteGeneratorStateInterface
{
    public function __construct(
        readonly protected TemplateDirectivesService $templateDirectivesService,
        readonly protected EventDispatcherInterface $eventDispatcher,
        readonly protected TemplateService $templateService,
        readonly protected DataHandler $dataHandler
    ) {
        parent::__construct();
    }

    /**
     * Update site template with the new uids.
     */
    public function process(SiteGeneratorWizard $context): void
    {
        // Update site template to set new uid
        $this->updateTemplate($context->getSiteData());
    }

    /**
     * Update site templates to set new uids.
     *
     * @param BaseDto $siteData New site data
     *
     * @throws Exception
     */
    protected function updateTemplate(BaseDto $siteData): void
    {
        // Cf. app/vendor/typo3/cms-tstemplate/Classes/Controller/ConstantEditorController.php
        $allTemplatesOnPage = $this->templateService->getAllTemplateRecordsOnPage($siteData->getHpPid());

        foreach ($allTemplatesOnPage as $template) {
            $rawTemplateConstantsArray = explode(LF, (string) $template['constants']);
            $constantPositions = $this->templateService->calculateConstantPositions($rawTemplateConstantsArray);

            $updatedTemplateConstantsArray = [];

            // For all constants, check if we need to update it
            foreach ($constantPositions as $key => $rawP) {
                // Looking for directives in comments
                $this->templateDirectivesService->lookForDirectives(($rawP > 0 ? $rawTemplateConstantsArray[$rawP - 1] : ''));
                $table = $this->templateDirectivesService->getTable('pages');

                $value = GeneralUtility::trimExplode('=', $rawTemplateConstantsArray[$rawP], false, 2);

                $uidsToExclude = GeneralUtility::trimExplode(
                    ',',
                    $this->templateDirectivesService->getIgnoreUids(),
                    true
                );
                $filteredMapping = $mapping = $siteData->getMappingArrayMerge($table);

                // Manage uids to exclude
                if ($uidsToExclude !== []) {
                    $filteredMapping = array_filter(
                        $mapping,
                        static fn($key) => !in_array((string) $key, $uidsToExclude, true),
                        ARRAY_FILTER_USE_KEY
                    );
                }

                $action = $this->templateDirectivesService->getAction('mapInList');
                $updatedValue = '';

                switch ($action) {
                    case 'mapInList':
                        $updatedValue = $this->mapInList($value[1], $filteredMapping);
                        break;
                    case 'mapInString':
                        $updatedValue = $this->mapInString($value[1], $filteredMapping);
                        break;
                    case 'exclude':
                        // Exclude all line
                        break;
                    default:
                        // Call custom action if there is one
                        $parameters = $this->templateDirectivesService->getParameters();
                        $event = $this->eventDispatcher->dispatch(new UpdateTemplateHPEvent(
                            $action,
                            $parameters,
                            $value[1],
                            $filteredMapping,
                            $this->templateDirectivesService
                        ));
                        $updatedValue = $event->getUpdatedValue();
                        break;
                }

                if (!empty($updatedValue)) {
                    $updatedTemplateConstantsArray[$rawP] = $updatedValue;
                }
            }

            if ($updatedTemplateConstantsArray !== []) {
                foreach ($updatedTemplateConstantsArray as $rowP => $updatedTemplateConstant) {
                    $rawTemplateConstantsArray[$rowP] = $this->templateService->updateValueInConf(
                        $rawTemplateConstantsArray[$rowP],
                        $updatedTemplateConstant
                    );
                }

                // Set the data to be saved
                $recordData = [];
                $templateUid = $template['_ORIG_uid'] ?? $template['uid'];
                $recordData['sys_template'][$templateUid]['constants'] = implode(LF, $rawTemplateConstantsArray);
                // Create new  tce-object
                $this->dataHandler->start($recordData, []);
                $this->dataHandler->process_datamap();

                // @extensionScannerIgnoreLine
                $siteData->addMessage($this->translate('generate.success.templateHpUpdated'));
                $this->log(LogLevel::NOTICE, 'Update home page template with new uids done');
            } else {
                // @extensionScannerIgnoreLine
                $siteData->addMessage($this->translate('generate.warning.templateHpUpdated'));
            }
        }
    }

    /**
     * Update constant in list.
     *
     * @return string Empty string or value updated
     */
    protected function mapInList(
        string $value,
        array $filteredMapping
    ): string {
        $functionName = '';

        // Check if the value in constant is a list of int - 78,125,98 - or just an int
        if (preg_match('/^\\s*([[:alpha:]]+)\\s*\\((.*)\\).*/', $value, $match)) {
            $functionName = $match[1];
            $value = $match[2];
        }

        if (preg_match('/^\d+(?:,\d+)*$/', $value)) {
            $updateConstant = false;

            $listOfInt = GeneralUtility::trimExplode(',', $value, true);

            // Set new uid for constants
            array_walk(
                $listOfInt,
                static function (&$constantValue) use ($filteredMapping, &$updateConstant) {
                    if (isset($filteredMapping[(int) $constantValue])) {
                        $constantValue = $filteredMapping[(int) $constantValue];
                        $updateConstant = true;
                    }
                }
            );

            if ($updateConstant) {
                //                return implode(',', $listOfInt);
                $newList = implode(',', $listOfInt);
                if ($functionName) {
                    $newList = "$functionName($newList)";
                }
                return $newList;
            }
        }
        return '';
    }

    /**
     * Update constants in string.
     *
     * @return string Empty string or value updated
     */
    protected function mapInString(
        string $value,
        array $filteredMapping
    ): string {
        $updateConstant = false;
        $count = 0;

        // A replacement could occur many times, for example, with $filteredMapping = [7 => 61, 6 => 62]
        // The result for addToList(6,7) will be addToList(62,621) instead of addToList(62,61)
        foreach ($filteredMapping as $modelUid => $siteUid) {
            $value = str_replace((string) $modelUid, (string) $siteUid, $value, $count);
            $updateConstant = ($updateConstant || $count > 0);
        }
        if ($updateConstant) {
            return $value;
        }
        return '';
    }
}
