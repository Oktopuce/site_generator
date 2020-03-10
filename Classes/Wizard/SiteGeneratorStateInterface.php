<?php

namespace Oktopuce\SiteGenerator\Wizard;

/***
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 ***/

interface SiteGeneratorStateInterface {
    public function process(SiteGeneratorWizard $context);
}
