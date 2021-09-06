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

namespace Oktopuce\SiteGenerator\Utility;

/**
 * ExtendedTemplateService
 */
class ExtendedTemplateService extends \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService
{

    /**
     * Get objectReg (data parsed from TS like plugin.tx_myplugin.settings.var = Z)
     * Z is not the value but the line number in $this->raw
     *
     * @return int[]
     */
    public function getObjReg()
    {
        return($this->objReg);
    }

}
