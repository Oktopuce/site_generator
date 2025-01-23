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
use TYPO3\CMS\Core\Database\Connection;

class BackendUserGroupRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
{
    /**
     * Sets the custom options field.
     *
     * @param int    $uid           The uid of the group to update
     * @param string $customOptions The new custom options
     */
    public function setCustomOptions(int $uid, string $customOptions): void
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
           ->update('be_groups')
           ->where(
               $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
           )->set('custom_options', $customOptions)->executeStatement();
    }

    /**
     * Gets the custom options field.
     *
     * @param int $uid The uid of the group
     *
     * @throws Exception
     *
     * @return string The current custom options
     */
    public function getCustomOptions(int $uid): string
    {
        $queryBuilder = $this->getQueryBuilder();

        $row = $queryBuilder->select('custom_options')
            ->from('be_groups')->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)))->executeQuery()
            ->fetchAssociative();

        return $row['custom_options'] ?? '';
    }

    /**
     * Sets the file mountpoints field.
     *
     * @param int    $uid        The uid of the group to update
     * @param string $fileMounts The new custom file mounts
     */
    public function setFileMounts(int $uid, string $fileMounts): void
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
           ->update('be_groups')
           ->where(
               $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
           )->set('file_mountpoints', $fileMounts)->executeStatement();
    }

    /**
     * Gets the file mountpoints field.
     *
     * @param int $uid The uid of the group
     *
     * @throws Exception
     *
     * @return string The current file mounts
     */
    public function getFileMounts(int $uid): string
    {
        $queryBuilder = $this->getQueryBuilder();

        $row = $queryBuilder->select('file_mountpoints')
            ->from('be_groups')->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)))->executeQuery()
            ->fetchAssociative();

        return $row['file_mountpoints'] ?? '';
    }
}
