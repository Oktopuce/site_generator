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
 * BaseDto Base DTO for data exchange beetwen form and Wizard
 *
 * @author Florian Rival <florian.typo3@oktopuce.fr>
 */
class BaseDto
{

    /**
     * First page title
     *
     * @var string
     */
    protected string $title = '';

    /**
     * Page UID where model will be copied
     *
     * @var int
     */
    protected int $pid = 0;

    /**
     * Model Pid to copy
     *
     * @var int
     */
    protected int $modelPid = 0;

    /**
     * Contains the relation between pid before copy (I.E. model pid) / after copy (i.e. newsite pid)
     *
     * @var array
     */
    protected array $mappingArrayMerge = [];

    /**
     * Success message displayed when process finished
     *
     * @var string
     */
    protected string $message = '';

    /**
     * Home page id
     *
     * @var int
     */
    protected int $hpPid = 0;

    /**
     * Title
     *
     * @param string $title

     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get title sanitize (used for folder creation)
     *
     * @return string
     */
    public function getTitleSanitize(): string
    {
        return(preg_replace('/[^a-z0-9]+/', '-', strtolower($this->title)));
    }

    /**
     * Pid
     *
     * @param int $pid
     * @return void
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * Get pid
     *
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Set the root site's pid
     *
     * @param int $modelPid
     * @return void
     */
    public function setModelPid(int $modelPid): void
    {
        $this->modelPid = $modelPid;
    }

    /**
     * Get modelPid
     *
     * @return int
     */
    public function getModelPid(): int
    {
        return $this->modelPid;
    }

    /**
     * Set the home page's pid
     *
     * @param int $hpPid
     * @return void
     */
    public function setHpPid(int $hpPid): void
    {
        $this->hpPid = $hpPid;
    }

    /**
     * Get hpPid
     *
     * @return int
     */
    public function getHpPid(): int
    {
        return $this->hpPid;
    }

    /**
     * Set the mapping array merge : relation between original pid / new pid after model copy
     *
     * @param array $mappingArrayMerge
     * @return void
     */
    public function setMappingArrayMerge(array $mappingArrayMerge): void
    {
        $this->mappingArrayMerge = $mappingArrayMerge;
    }

    /**
     * Get mappingArrayMerge : relation between original pid / new pid after model copy
     *
     * @param string $key The key to use 'page', 'tt_content', etc. if empty, return all data
     * @return array
     */
    public function getMappingArrayMerge(string $key = ''): array
    {
        return (empty($key) ? $this->mappingArrayMerge : ($this->mappingArrayMerge[$key] ?? []));
    }

    /**
     * Get new pid from model pid
     * @param int $modelPid The pid in model
     *
     * @return int
     */
    public function getNewPidFromModel(int $modelPid): int
    {
        return ($this->mappingArrayMerge[$modelPid] ?? 0);
    }

    /**
     * Set a message
     *
     * @param string $message

     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Append to success message
     *
     * @param string $message

     * @return void
     */
    public function addMessage(string $message): void
    {
        $this->message .= PHP_EOL . $message;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

}
