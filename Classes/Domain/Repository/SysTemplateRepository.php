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

namespace Oktopuce\SiteGenerator\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SysTemplateRepository
{
    /**
     * Find a systemplate by pid (consider there's only one template)
     *
     * @param int $pid The page id where the template is located
     * @return array
     * @throws Exception
     */
    public function findByPid(int $pid): array
    {
        $queryBuilder = $this->getQueryBuilder();

        $row = $queryBuilder->select('uid', 'constants')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        return (($row !== false) ? $row : []);
    }

    /**
     * SetConstants
     *
     * @param int $uid The uid of the template to update
     * @param string $constants The new constants
     * @return void
     */
    public function setConstants(int $uid, string $constants): void
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->update('sys_template')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->set('constants', $constants)
            ->executeStatement();
    }

    /**
     * Returns an instance of the QueryBuilder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        /** @var ConnectionPool $pool */
        $pool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $pool->getQueryBuilderForTable('sys_template');
    }

}
