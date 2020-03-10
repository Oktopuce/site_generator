<?php

namespace Oktopuce\SiteGenerator\Dto;

/*
 * This file is part of the Extension "SiteGenerator" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
     * Domain
     *
     * @param string $domain

     * @return void
     */
    public function setDomain($domain): void
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
    public function setMountId($mountId): void
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
    public function setBeGroupId($beGroupId): void
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
    public function setGroupPrefix($groupPrefix): void
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
    public function setBaseFolderName($baseFolderName): void
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
    public function setCommonMountPointUid($commonMountPointUid): void
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
    public function setSubFolderNames($subFolderNames): void
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
    public function setFeGroupId($feGroupId): void
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
    public function setFeGroupPid($feGroupPid): void
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

}
