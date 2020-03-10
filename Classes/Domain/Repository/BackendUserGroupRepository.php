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

class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{
    /**
     * Sets the custom options field.
     *
     * @param int $uid  The uid of the group to update
     * @param string $customOptions The new custom options
     * @return void
     */
    public function setCustomOptions($uid, $customOptions)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
           ->update('be_groups')
           ->where(
              $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
           )
           ->set('custom_options', $customOptions)
           ->execute();
    }

    /**
     * Gets the custom options field.
     *
     * @param int $uid  The uid of the group
     * @return string The current custom options
     */
    public function getCustomOptions($uid): string
    {
        $queryBuilder = $this->getQueryBuilder();

        $row = $queryBuilder->select('custom_options')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetch();

        return ($row['custom_options'] ?? '');
    }

    /**
     * Sets the file mountpoints field.
     *
     * @param int $uid  The uid of the group to update
     * @param string $fileMounts The new custom file mounts
     * @return void
     */
    public function setFileMounts($uid, $fileMounts)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
           ->update('be_groups')
           ->where(
              $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
           )
           ->set('file_mountpoints', $fileMounts)
           ->execute();
    }

    /**
     * Gets the file mountpoints field.
     *
     * @param int $uid  The uid of the group
     * @return string The current file mounts
     */
    public function getFileMounts($uid): string
    {
        $queryBuilder = $this->getQueryBuilder();

        $row = $queryBuilder->select('file_mountpoints')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetch();

        return ($row['file_mountpoints'] ?? '');
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
		return $pool->getQueryBuilderForTable('be_groups');
	}

}
