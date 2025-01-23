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
 * SiteGeneratorDto DTO for data exchange beetwen form and Wizard.
 *
 * @author Florian Rival <florian.typo3@oktopuce.fr>
 */
class SiteGeneratorDto extends BaseDto
{
    /**
     * Domain name.
     *
     * @var string
     */
    protected string $domain = '';

    /**
     * Mount point uid.
     *
     * @var int
     */
    protected int $mountId = 0;

    /**
     * Group prefix.
     *
     * @var string
     */
    protected string $groupPrefix = '';

    /**
     * BE group Uid.
     *
     * @var int
     */
    protected int $beGroupId = 0;

    /**
     * Common mount point uid for all groups.
     *
     * @var int
     */
    protected int $commonMountPointUid = 0;

    /**
     * Base folder name.
     *
     * @var string
     */
    protected string $baseFolderName = '';

    /**
     * Sub folder names.
     *
     * @var string
     */
    protected string $subFolderNames = '';

    /**
     * FE group Uid.
     *
     * @var int
     */
    protected int $feGroupId = 0;

    /**
     * Pid for FE group.
     *
     * @var int
     */
    protected int $feGroupPid = 0;

    /**
     * Domain.
     *
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Get domain.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Set the mount point ID.
     *
     * @param int $mountId
     */
    public function setMountId(int $mountId): void
    {
        $this->mountId = $mountId;
    }

    /**
     * Get mountId.
     *
     * @return int
     */
    public function getMountId(): int
    {
        return $this->mountId;
    }

    /**
     * Set the user BE group ID.
     *
     * @param int $beGroupId
     */
    public function setBeGroupId(int $beGroupId): void
    {
        $this->beGroupId = $beGroupId;
    }

    /**
     * Get beGroupId.
     *
     * @return int
     */
    public function getBeGroupId(): int
    {
        return $this->beGroupId;
    }

    /**
     * Set group prefix.
     *
     * @param string $groupPrefix
     */
    public function setGroupPrefix(string $groupPrefix): void
    {
        $this->groupPrefix = $groupPrefix;
    }

    /**
     * Get groupPrefix.
     *
     * @return string
     */
    public function getGroupPrefix(): string
    {
        return $this->groupPrefix;
    }

    /**
     * Set baseFolderName.
     *
     * @param string $baseFolderName
     */
    public function setBaseFolderName(string $baseFolderName): void
    {
        $this->baseFolderName = $baseFolderName;
    }

    /**
     * Get baseFolderName.
     *
     * @return string
     */
    public function getBaseFolderName(): string
    {
        return $this->baseFolderName;
    }

    /**
     * Set commonMountPointUid.
     *
     * @param int $commonMountPointUid
     */
    public function setCommonMountPointUid(int $commonMountPointUid): void
    {
        $this->commonMountPointUid = $commonMountPointUid;
    }

    /**
     * Get commonMountPointUid.
     *
     * @return int
     */
    public function getCommonMountPointUid(): int
    {
        return $this->commonMountPointUid;
    }

    /**
     * Set subFolderNames.
     *
     * @param string $subFolderNames
     */
    public function setSubFolderNames(string $subFolderNames): void
    {
        $this->subFolderNames = $subFolderNames;
    }

    /**
     * Get subFolderNames.
     *
     * @return string
     */
    public function getSubFolderNames(): string
    {
        return $this->subFolderNames;
    }

    /**
     * Set the feGroupId.
     *
     * @param int $feGroupId
     */
    public function setFeGroupId(int $feGroupId): void
    {
        $this->feGroupId = $feGroupId;
    }

    /**
     * Get feGroupId.
     *
     * @return int
     */
    public function getFeGroupId(): int
    {
        return $this->feGroupId;
    }

    /**
     * Set the feGroupPid.
     *
     * @param int $feGroupPid
     */
    public function setFeGroupPid(int $feGroupPid): void
    {
        $this->feGroupPid = $feGroupPid;
    }

    /**
     * Get feGroupPid.
     *
     * @return int
     */
    public function getFeGroupPid(): int
    {
        return $this->feGroupPid;
    }

}
