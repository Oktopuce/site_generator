<?php

namespace Oktopuce\SiteGenerator\Domain\Repository;

/* * *
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * * */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

class PagesRepository
{

    /**
     * Update page
     *
     * @param int $uid  The page record to update
     * @param array $updateValues  The columns/values to set
     * @return void
     */
    public function updatePage($uid, $updateValues): void
    {
        $queryBuilder = $this->getQueryBuilder();

        // Remove all restrictions but add DeletedRestriction again
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->update('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            );

        foreach ($updateValues as $identifier => $value) {
            $queryBuilder->set($identifier, $value);
        }

        $queryBuilder->execute();
    }

    /**
     * Get pages title
     *
     * @param  string $uids Uid comma separated
     *
     * @return array
     */
    public function getPages($uids): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $pages = $queryBuilder->select('uid', 'title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter(GeneralUtility::intExplode(',', $uids, true), Connection::PARAM_INT_ARRAY))
            )
            ->execute()
            ->fetchAll();

        return($pages);
    }

    /**
     * Returns an instance of the QueryBuilder.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        /** @var ConnectionPool $pool */
        $pool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $pool->getQueryBuilderForTable('pages');
    }

}
