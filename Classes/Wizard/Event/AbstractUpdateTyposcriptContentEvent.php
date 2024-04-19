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

namespace Oktopuce\SiteGenerator\Wizard\Event;

use Oktopuce\SiteGenerator\Utility\TemplateDirectivesService;

/**
 * This event is fired when state updating record with TypoScript (StateUpdateTemplateHP etc) is executed and action is a custom action
 * i.e. : different from : mapInList, mapInString or exclude
 */
abstract class AbstractUpdateTyposcriptContentEvent
{
    /**
     * @var string
     */
    private string $updatedValue = '';

    /**
     * @var string
     */
    private string $action;

    /**
     * @var string
     */
    private string $parameters;

    /**
     * @var array
     */
    private array $filteredMapping;

    /**
     * @var string
     */
    private string $value;

    /**
     * @var TemplateDirectivesService
     */
    private TemplateDirectivesService $templateDirectivesService;

    /**
     * @param string $action The action
     * @param string $parameters The parameter
     * @param string $value Current value
     * @param array $filteredMapping Mapping filtered - i.e. ignoredUids already removed
     * @param TemplateDirectivesService $templateDirectivesService
     */
    public function __construct(string $action, string $parameters, string $value, array $filteredMapping, TemplateDirectivesService $templateDirectivesService)
    {
        $this->action = $action;
        $this->parameters = $parameters;
        $this->value = $value;
        $this->filteredMapping = $filteredMapping;
        $this->templateDirectivesService = $templateDirectivesService;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getParameters(): string
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getFilteredMapping(): array
    {
        return $this->filteredMapping;
    }

    /**
     * @return TemplateDirectivesService
     */
    public function getTemplateDirectivesService(): TemplateDirectivesService
    {
        return $this->templateDirectivesService;
    }

    /**
     * Get the updated value
     *
     * @return string Empty if no update required otherwise the value to update
     */
    public function getUpdatedValue(): string
    {
        return $this->updatedValue;
    }

    /**
     * Set the updated value
     *
     * @param string $updatedValue The value to update
     * @return void
     */
    public function setUpdatedValue(string $updatedValue): void
    {
        $this->updatedValue = $updatedValue;
    }
}
