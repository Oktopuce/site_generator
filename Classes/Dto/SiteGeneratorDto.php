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

 namespace Oktopuce\SiteGenerator\Dto;

/**
 * SiteGeneratorDto DTO for data exchange beetwen form and Wizard
 *
 * @author Florian Rival <florian.typo3@oktopuce.fr>
 */
class SiteGeneratorDto extends BaseDto
{

    /**
     * Domain name
     *
     * @var string
     */
    protected $domain = '';

    /**
     * Mount point uid
     *
     * @var int
     */
    protected $mountId = 0;

    /**
     * Group prefix
     *
     * @var string
     */
    protected $groupPrefix = '';

    /**
     * BE group Uid
     *
     * @var int
     */
    protected $beGroupId = 0;

    /**
     * Common mount point uid for all groups
     *
     * @var int
     */
    protected $commonMountPointUid = 0;

    /**
     * Base folder name
     *
     * @var string
     */
    protected $baseFolderName = '';

    /**
     * Sub folder names
     *
     * @var string
     */
    protected $subFolderNames = '';

    /**
     * FE group Uid
     *
     * @var int
     */
    protected $feGroupId = 0;

    /**
     * Pid for FE group
     *
     * @var int
     */
    protected $feGroupPid = 0;

    /**
     * @var bool
     */
    protected $groupHomePath = false;

    /**
     * Domain
     *
     * @param string $domain

     * @return void
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Set the mount point ID
     *
     * @param int $mountId
     * @return void
     */
    public function setMountId(int $mountId): void
    {
        $this->mountId = $mountId;
    }

    /**
     * Get mountId
     *
     * @return int
     */
    public function getMountId(): int
    {
        return $this->mountId;
    }

    /**
     * Set the user BE group ID
     *
     * @param int $beGroupId
     * @return void
     */
    public function setBeGroupId(int $beGroupId): void
    {
        $this->beGroupId = $beGroupId;
    }

    /**
     * Get beGroupId
     *
     * @return int
     */
    public function getBeGroupId(): int
    {
        return $this->beGroupId;
    }

    /**
     * Set group prefix
     *
     * @param string $groupPrefix
     * @return void
     */
    public function setGroupPrefix(string $groupPrefix): void
    {
        $this->groupPrefix = $groupPrefix;
    }

    /**
     * Get groupPrefix
     *
     * @return string
     */
    public function getGroupPrefix(): string
    {
        return $this->groupPrefix;
    }

    /**
     * Set baseFolderName
     *
     * @param string $baseFolderName
     * @return void
     */
    public function setBaseFolderName(string $baseFolderName): void
    {
        $this->baseFolderName = $baseFolderName;
    }

    /**
     * Get baseFolderName
     *
     * @return string
     */
    public function getBaseFolderName(): string
    {
        return $this->baseFolderName;
    }

    /**
     * Set commonMountPointUid
     *
     * @param int $commonMountPointUid
     * @return void
     */
    public function setCommonMountPointUid(int $commonMountPointUid): void
    {
        $this->commonMountPointUid = $commonMountPointUid;
    }

    /**
     * Get commonMountPointUid
     *
     * @return int
     */
    public function getCommonMountPointUid(): int
    {
        return $this->commonMountPointUid;
    }

    /**
     * Set subFolderNames
     *
     * @param string $subFolderNames
     * @return void
     */
    public function setSubFolderNames(string $subFolderNames): void
    {
        $this->subFolderNames = $subFolderNames;
    }

    /**
     * Get subFolderNames
     *
     * @return string
     */
    public function getSubFolderNames(): string
    {
        return $this->subFolderNames;
    }

    /**
     * Set the feGroupId
     *
     * @param int $feGroupId
     * @return void
     */
    public function setFeGroupId(int $feGroupId): void
    {
        $this->feGroupId = $feGroupId;
    }

    /**
     * Get feGroupId
     *
     * @return int
     */
    public function getFeGroupId(): int
    {
        return $this->feGroupId;
    }

    /**
     * Set the feGroupPid
     *
     * @param int $feGroupPid
     * @return void
     */
    public function setFeGroupPid(int $feGroupPid): void
    {
        $this->feGroupPid = $feGroupPid;
    }

    /**
     * Get feGroupPid
     *
     * @return int
     */
    public function getFeGroupPid(): int
    {
        return $this->feGroupPid;
    }

    /**
     * @return bool
     */
    public function getGroupHomePath(): bool
    {
        return $this->groupHomePath;
    }

    /**
     * @param bool $groupHomePath
     */
    public function setGroupHomePath(bool $groupHomePath): void
    {
        $this->groupHomePath = $groupHomePath;
    }
}
