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
 * BaseDto Base DTO for data exchange between form and Wizard.
 *
 * @author Florian Rival <florian.typo3@oktopuce.fr>
 */
class BaseDto
{
    /**
     * First page title.
     */
    protected string $title = '';

    /**
     * Page UID where model will be copied.
     */
    protected int $pid = 0;

    /**
     * Model Pid to copy.
     */
    protected int $modelPid = 0;

    /**
     * Contains the relation between pid before copy (I.E. model pid) / after copy (i.e. new-site pid).
     */
    protected array $mappingArrayMerge = [];

    /**
     * Success message displayed when process finished.
     */
    protected string $message = '';

    /**
     * Home page id.
     */
    protected int $hpPid = 0;

    /**
     * Title.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get title sanitize (used for folder creation).
     */
    public function getTitleSanitize(): string
    {
        return preg_replace('/[^a-z0-9]+/', '-', strtolower($this->title));
    }

    /**
     * Pid.
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * Get pid.
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Set the root site's pid.
     */
    public function setModelPid(int $modelPid): void
    {
        $this->modelPid = $modelPid;
    }

    /**
     * Get modelPid.
     */
    public function getModelPid(): int
    {
        return $this->modelPid;
    }

    /**
     * Set the home page's pid.
     */
    public function setHpPid(int $hpPid): void
    {
        $this->hpPid = $hpPid;
    }

    /**
     * Get hpPid.
     */
    public function getHpPid(): int
    {
        return $this->hpPid;
    }

    /**
     * Set the mapping array merge : relation between original pid / new pid after model copy.
     */
    public function setMappingArrayMerge(array $mappingArrayMerge): void
    {
        $this->mappingArrayMerge = $mappingArrayMerge;
    }

    /**
     * Get mappingArrayMerge : relation between original pid / new pid after model copy.
     *
     * @param string $key The key to use 'page', 'tt_content', etc. if empty, return all data
     */
    public function getMappingArrayMerge(string $key = ''): array
    {
        return $key === '' || $key === '0' ? $this->mappingArrayMerge : ($this->mappingArrayMerge[$key] ?? []);
    }

    /**
     * Get new pid from model pid.
     *
     * @param int $modelPid The pid in model
     */
    public function getNewPidFromModel(int $modelPid): int
    {
        return $this->mappingArrayMerge[$modelPid] ?? 0;
    }

    /**
     * Set a message.
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Append to success message.
     */
    public function addMessage(string $message): void
    {
        $this->message .= PHP_EOL . $message;
    }

    /**
     * Get message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

}
