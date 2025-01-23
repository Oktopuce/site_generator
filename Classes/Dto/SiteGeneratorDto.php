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
     */
    protected string $domain = '';

    /**
     * Mount point uid.
     */
    protected int $mountId = 0;

    /**
     * Group prefix.
     */
    protected string $groupPrefix = '';

    /**
     * BE group Uid.
     */
    protected int $beGroupId = 0;

    /**
     * Common mount point uid for all groups.
     */
    protected int $commonMountPointUid = 0;

    /**
     * Base folder name.
     */
    protected string $baseFolderName = '';

    /**
     * Sub folder names.
     */
    protected string $subFolderNames = '';

    /**
     * FE group Uid.
     */
    protected int $feGroupId = 0;

    /**
     * Pid for FE group.
     */
    protected int $feGroupPid = 0;

    /**
     * Domain.
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Get domain.
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Set the mount point ID.
     */
    public function setMountId(int $mountId): void
    {
        $this->mountId = $mountId;
    }

    /**
     * Get mountId.
     */
    public function getMountId(): int
    {
        return $this->mountId;
    }

    /**
     * Set the user BE group ID.
     */
    public function setBeGroupId(int $beGroupId): void
    {
        $this->beGroupId = $beGroupId;
    }

    /**
     * Get beGroupId.
     */
    public function getBeGroupId(): int
    {
        return $this->beGroupId;
    }

    /**
     * Set group prefix.
     */
    public function setGroupPrefix(string $groupPrefix): void
    {
        $this->groupPrefix = $groupPrefix;
    }

    /**
     * Get groupPrefix.
     */
    public function getGroupPrefix(): string
    {
        return $this->groupPrefix;
    }

    /**
     * Set baseFolderName.
     */
    public function setBaseFolderName(string $baseFolderName): void
    {
        $this->baseFolderName = $baseFolderName;
    }

    /**
     * Get baseFolderName.
     */
    public function getBaseFolderName(): string
    {
        return $this->baseFolderName;
    }

    /**
     * Set commonMountPointUid.
     */
    public function setCommonMountPointUid(int $commonMountPointUid): void
    {
        $this->commonMountPointUid = $commonMountPointUid;
    }

    /**
     * Get commonMountPointUid.
     */
    public function getCommonMountPointUid(): int
    {
        return $this->commonMountPointUid;
    }

    /**
     * Set subFolderNames.
     */
    public function setSubFolderNames(string $subFolderNames): void
    {
        $this->subFolderNames = $subFolderNames;
    }

    /**
     * Get subFolderNames.
     */
    public function getSubFolderNames(): string
    {
        return $this->subFolderNames;
    }

    /**
     * Set the feGroupId.
     */
    public function setFeGroupId(int $feGroupId): void
    {
        $this->feGroupId = $feGroupId;
    }

    /**
     * Get feGroupId.
     */
    public function getFeGroupId(): int
    {
        return $this->feGroupId;
    }

    /**
     * Set the feGroupPid.
     */
    public function setFeGroupPid(int $feGroupPid): void
    {
        $this->feGroupPid = $feGroupPid;
    }

    /**
     * Get feGroupPid.
     */
    public function getFeGroupPid(): int
    {
        return $this->feGroupPid;
    }

}
