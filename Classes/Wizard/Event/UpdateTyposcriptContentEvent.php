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
 * This event is fired when state updating record with TypoScript (StateUpdateTemplateHP etc) is executed and action is a custom action
 * i.e. : different from : mapInList, mapInString or exclude
 */
class UpdateTyposcriptContentEvent extends AbstractUpdateTyposcriptContentEvent {}
