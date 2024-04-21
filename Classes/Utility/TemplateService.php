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

namespace Oktopuce\SiteGenerator\Utility;

use Doctrine\DBAL\Exception;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Wizard\Event\UpdateTyposcriptContentEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Cf. app/vendor/typo3/cms-tstemplate/Classes/Controller/ConstantEditorController.php
class TemplateService
{
    public function __construct(
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly ConnectionPool            $connectionPool,
        private readonly TemplateDirectivesService $templateDirectivesService,
        private readonly DataHandler               $dataHandler
    )
    {
    }

    /**
     * Get an array of all template records on a page.
     * @throws Exception
     */
    public function getAllTemplateRecordsOnPage(int $pageId): array
    {
        if (!$pageId) {
            return [];
        }
        $result = $this->getTemplateQueryBuilder($pageId)->executeQuery();
        $templateRows = [];
        while ($row = $result->fetchAssociative()) {
            $templateRows[] = $row;
        }
        return $templateRows;
    }

    /**
     * Helper method to prepare the query builder for getting sys_template records from a given pid.
     */
    protected function getTemplateQueryBuilder(int $pid): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        return $queryBuilder->select('*')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
            )
            ->orderBy($GLOBALS['TCA']['sys_template']['ctrl']['sortby']);
    }

    public function calculateConstantPositions(
        array $rawTemplateConstantsArray,
        array &$constantPositions = [],
        string $prefix = '',
        int $braceLevel = 0,
        int &$lineCounter = 0
    ): array {
        while (isset($rawTemplateConstantsArray[$lineCounter])) {
            $line = ltrim($rawTemplateConstantsArray[$lineCounter]);
            $lineCounter++;
            if (!$line || $line[0] === '[') {
                // Ignore empty lines and conditions
                continue;
            }
            if (strcspn($line, '}#/') !== 0) {
                $operatorPosition = strcspn($line, ' {=<');
                $key = substr($line, 0, $operatorPosition);
                $line = ltrim(substr($line, $operatorPosition));
                if ($line[0] === '=') {
                    $constantPositions[$prefix . $key . "_" . $lineCounter] = $lineCounter - 1;
                } elseif ($line[0] === '{') {
                    $braceLevel++;
                    $this->calculateConstantPositions($rawTemplateConstantsArray, $constantPositions, $prefix . $key . '.', $braceLevel, $lineCounter);
                }
            } elseif ($line[0] === '}') {
                $braceLevel--;
                if ($braceLevel < 0) {
                    $braceLevel = 0;
                } else {
                    // Leaving this brace level: Force return to caller recursion
                    break;
                }
            }
        }
        return $constantPositions;
    }

    /**
     * Update a constant value
     *
     * @param string $rawConstant The raw constant line
     * @param string $var The new value
     * @return string
     */
    public function updateValueInConf(string $rawConstant, string $var): string
    {
        $theValue = ' ' . trim($var);
        $parts = explode('=', $rawConstant, 2);
        if (count($parts) === 2) {
            $parts[1] = $theValue;
        }
        return implode('=', $parts);
    }

    /**
     * @param string $table Name database table
     * @param array $record Record data to update
     * @param string $contentField Name of field which contains the content to update
     * @param BaseDto $siteData
     * @return bool true if content was update, false if no data was updated
     */
    public function updateContent(string $table, array $record, string $contentField, BaseDto $siteData): bool
    {
        $rawTemplateConstantsArray = explode(LF, $record[$contentField] ?? '');
        $constantPositions = $this->calculateConstantPositions($rawTemplateConstantsArray);

        $updatedTemplateConstantsArray = [];
        $directivesPositions = [];

        // For all constants, check if we need to update it
        foreach ($constantPositions as $key => $rawP) {
            // Looking for directives in comments
            if($this->templateDirectivesService->lookForDirectives(($rawP > 0 ? $rawTemplateConstantsArray[$rawP - 1] : ''))) {
                $directivesPositions[] = $rawP - 1;
            };

            $value = GeneralUtility::trimExplode('=', $rawTemplateConstantsArray[$rawP]);

            $uidsToExclude = GeneralUtility::trimExplode(',',
                $this->templateDirectivesService->getIgnoreUids(), true);
            $filteredMapping = $mapping = $siteData->getMappingArrayMerge(
                $this->templateDirectivesService->getTable('pages')
            );

            // Manage uids to exclude
            if (!empty($uidsToExclude)) {
                $filteredMapping = array_filter($mapping, static function ($key) use ($uidsToExclude) {
                    return !in_array((string)$key, $uidsToExclude, true);
                }, ARRAY_FILTER_USE_KEY);
            }

            $action = $this->templateDirectivesService->getAction('mapInList');
            $updatedValue = '';

            switch ($action) {
                case 'mapInList' :
                    $updatedValue = $this->mapInList($value[1], $filteredMapping);
                    break;
                case 'mapInString' :
                    $updatedValue = $this->mapInString($value[1], $filteredMapping);
                    break;
                case 'exclude' :
                    // Exclude all line
                    break;
                default :
                    // Call custom action if there is one
                    $parameters = $this->templateDirectivesService->getParameters();
                    $event = $this->eventDispatcher->dispatch(new UpdateTyposcriptContentEvent($action, $parameters,
                        $value[1], $filteredMapping, $this->templateDirectivesService));
                    $updatedValue = $event->getUpdatedValue();
                    break;
            }

            if (!empty($updatedValue)) {
                $updatedTemplateConstantsArray[$rawP] = $updatedValue;
            }
        }

        if ($updatedTemplateConstantsArray) {
            foreach ($updatedTemplateConstantsArray as $rowP => $updatedTemplateConstant) {
                $rawTemplateConstantsArray[$rowP] = $this->updateValueInConf($rawTemplateConstantsArray[$rowP], $updatedTemplateConstant);
            }
            if($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator']['removeDirectivesFromOutput'] ?? true) {
                // Remove directives from output
                $rawTemplateConstantsArray = array_diff_key($rawTemplateConstantsArray, array_flip($directivesPositions));
            }

            // Set the data to be saved
            $recordData = [];
            $recordUid = $record['_ORIG_uid'] ?? $record['uid'];
            $recordData[$table][$recordUid][$contentField] = implode(LF, $rawTemplateConstantsArray);
            // Create new  tce-object
            $this->dataHandler->start($recordData, []);
            $this->dataHandler->process_datamap();
            return true;
        }
        return false;
    }

    /**
     * Update constant in list
     *
     * @param string $value
     * @param array $filteredMapping
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
            array_walk($listOfInt,
                static function (&$constantValue) use ($filteredMapping, &$updateConstant) {
                    if (isset($filteredMapping[(int)$constantValue])) {
                        $constantValue = $filteredMapping[(int)$constantValue];
                        $updateConstant = true;
                    }
                });
            if ($updateConstant) {
                $newList = implode(',', $listOfInt);
                if($functionName) {
                    $newList = "$functionName($newList)";
                }
                return $newList;
            }
        }
        return ('');
    }

    /**
     * Update constants in string
     *
     * @param string $value
     * @param array $filteredMapping
     * @return string Empty string or value updated
     */
    protected function mapInString(
        string $value,
        array $filteredMapping
    ): string {
        $updateConstant = false;
        $count = 0;

        foreach ($filteredMapping as $modelUid => $siteUid) {
            $value = str_replace((string)$modelUid, (string)$siteUid, $value, $count);
            $updateConstant = ($updateConstant || $count > 0);
        }
        if ($updateConstant) {
            return ($value);
        }
        return ('');
    }
}
