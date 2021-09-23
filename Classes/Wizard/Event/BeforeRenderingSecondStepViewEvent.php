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
 * This event is fired before rendering the second form for gathering data
 * It is usefull when you use your own template and want to assign more variables to the view
 */
final class BeforeRenderingSecondStepViewEvent
{
    use BeforeRenderingiewTrait;
}
