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
 * This event is fired when state StateUpdateTemplateHP is executed and action is a custom action
 * i.e. : different from : mapInList, mapInString or exclude.
 */
final class UpdateTemplateHPEvent
{
    private string $updatedValue = '';

    /**
     * @param string $action          The action
     * @param string $parameters      The parameter
     * @param string $value           Current value
     * @param array  $filteredMapping Mapping filtered - i.e. ignoredUids already removed
     */
    public function __construct(
        private readonly string $action,
        private readonly string $parameters,
        private readonly string $value,
        private readonly array $filteredMapping,
        private readonly TemplateDirectivesService $templateDirectivesService
    ) {}

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParameters(): string
    {
        return $this->parameters;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFilteredMapping(): array
    {
        return $this->filteredMapping;
    }

    public function getTemplateDirectivesService(): TemplateDirectivesService
    {
        return $this->templateDirectivesService;
    }

    /**
     * Get the updated value.
     *
     * @return string Empty if no update required otherwise the value to update
     */
    public function getUpdatedValue(): string
    {
        return $this->updatedValue;
    }

    /**
     * Set the updated value.
     *
     * @param string $updatedValue The value to update
     */
    public function setUpdatedValue(string $updatedValue): void
    {
        $this->updatedValue = $updatedValue;
    }
}
