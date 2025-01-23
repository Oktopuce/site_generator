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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

class PagesRepository
{
    /**
     * Update page.
     *
     * @param int   $uid          The page record to update
     * @param array $updateValues The columns/values to set
     */
    public function updatePage(int $uid, array $updateValues): void
    {
        $queryBuilder = $this->getQueryBuilder();

        // Remove all restrictions but add DeletedRestriction again
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->update('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            );

        foreach ($updateValues as $identifier => $value) {
            $queryBuilder->set($identifier, $value);
        }

        $queryBuilder->executeStatement();
    }

    /**
     * Get pages title.
     *
     * @param string $uids Uid comma separated
     *
     * @throws Exception
     */
    public function getPages(string $uids): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        return $queryBuilder->select('uid', 'title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter(GeneralUtility::intExplode(',', $uids, true), Connection::PARAM_INT_ARRAY))
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Find a root site id between a set of pages.
     *
     * @param array $uids The pages ids to look for a root line
     *
     * @throws Exception
     */
    public function getRootSiteId(array $uids): int
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $rootPage = $queryBuilder->select('uid')
            ->from('pages')
            ->setMaxResults(1)
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY)),
                $queryBuilder->expr()->eq('is_siteroot', true)
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return $rootPage === [] ? 0 : $rootPage[0]['uid'];
    }

    /**
     * Returns an instance of the QueryBuilder.
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        /** @var ConnectionPool $pool */
        $pool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $pool->getQueryBuilderForTable('pages');
    }
}
