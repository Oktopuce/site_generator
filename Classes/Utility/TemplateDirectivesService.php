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

class TemplateDirectivesService
{
    /**
     * @var array
     */
    protected array $directives = [];

    /**
     * Search for directives in constant comment, sample :
     * # ext=SiteGenerator; table=tt_content; action=mapInString; ignoreUids=777,888
     *
     * Available directives :
     * ext=SiteGenerator : mandatory
     * table=(page | tt_content | tx_mytable) : the table used for the mapping
     * action=(mapInString | mapInList | exclude)
     * parameters=my_params : parameters for a custom directive
     * ignoreUids=777,888 : list of uids to ignore
     *
     * @param string $lineFromConstant The constant line
     */
    public function lookForDirectives(string $lineFromConstant): void
    {
        // Remove all spaces and line feed from input line
        $inputLine = trim(str_replace(' ', '', $lineFromConstant));
        $directivesPattern = '(?:;(action|table|ignoreUids|parameters)=([^;=]+))?';
        $regEx = "/^(?:#ext=SiteGenerator)$directivesPattern$directivesPattern$directivesPattern$directivesPattern;?/";
        $matchDirective = [];
        $this->directives = [];

        if (preg_match($regEx, $inputLine, $matchDirective)) {
            for ($i = 1, $iMax = count($matchDirective); $i < $iMax; $i += 2) {
                if (isset($matchDirective[$i], $matchDirective[$i + 1])) {
                    $this->directives[$matchDirective[$i]] = $matchDirective[$i + 1];
                }
            }
        }
    }

    /**
     * Return the name of the action in directives.
     *
     * @param string $default Default value
     *
     * @return string
     */
    public function getAction(string $default = ''): string
    {
        return $this->directives['action'] ?? $default;
    }

    /**
     * Get the parameters, for custom extensions, can be used with action
     * # ext=SiteGenerator; action=customAction; parameters=myparams.
     *
     * @return string
     */
    public function getParameters(): string
    {
        return $this->directives['parameters'] ?? '';
    }

    /**
     * Return the name of the table in directives.
     *
     * @param string $default Default value
     *
     * @return string
     */
    public function getTable(string $default = ''): string
    {
        return $this->directives['table'] ?? $default;
    }

    /**
     * Return the list of uids to ignore.
     *
     * @param string $default Default values - comma separated (sample : 12,54)
     *
     * @return string
     */
    public function getIgnoreUids(string $default = ''): string
    {
        return $this->directives['ignoreUids'] ?? $default;
    }
}
