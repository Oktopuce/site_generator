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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Cf. app/vendor/typo3/cms-tstemplate/Classes/Controller/ConstantEditorController.php
class TemplateService
{
    public function __construct(private readonly ConnectionPool $connectionPool) {}

    /**
     * Get an array of all template records on a page.
     *
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
            $line = ltrim((string) $rawTemplateConstantsArray[$lineCounter]);
            $lineCounter++;
            if (!$line || $line[0] === '[') {
                // Ignore empty lines and conditions
                continue;
            }
            if (strcspn($line, '}#/') !== 0) {
                $operatorPosition = strcspn($line, ' {=<');
                $key = substr($line, 0, $operatorPosition);
                $line = ltrim(substr($line, $operatorPosition));
                if ($line[0] === '=' || str_starts_with($line, ':=')) {
                    $constantPositions[$prefix . $key . '_' . $lineCounter] = $lineCounter - 1;
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
     * Update a constant value.
     *
     * @param string $rawConstant The raw constant line
     * @param string $var         The new value
     *
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
}
