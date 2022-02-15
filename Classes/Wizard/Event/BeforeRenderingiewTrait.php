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

/**
 * This event is fired before rendering the first form for gathering data
 * It is useful when you use your own template and want to assign more variables to the view
 */
trait BeforeRenderingiewTrait
{
    /**
     * @var array
     */
    private array $viewVariables;

    public function __construct(array $viewVariables)
    {
        $this->viewVariables = $viewVariables;
    }

    /**
     * getViewVariables
     *
     * @return array
     */
    public function getViewVariables(): array
    {
        return $this->viewVariables;
    }

    /**
     * addViewVariables
     *
     * @param  array $variables
     * @return void
     */
    public function addViewVariables(array $variables): void
    {
        $this->viewVariables += $variables;
    }
}
