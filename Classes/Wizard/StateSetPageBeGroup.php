<?php

namespace Oktopuce\SiteGenerator\Wizard;

/* * *
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * * */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use Oktopuce\SiteGenerator\Dto\BaseDto;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;

/**
 * StateSetPageBeGroup : set page access
 */
class StateSetPageBeGroup extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Set group BE to all pages
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context)
    {
        // Affect BE group to pages created
        $this->setBeGroup($context->getSiteData());
    }

    /**
     * Affect BE group to pages created
     *
     * @param BaseDto $siteData New site data
     * @throws \Exception
     *
     * @return void
     */
    protected function setBeGroup(BaseDto $siteData): void
    {
        if ($siteData->getBeGroupId()) {
            $pages = [];
            foreach ($siteData->getMappingArrayMerge() as $sitePid) {
                /* @var $pagesRepository PagesRepository */
                $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);

                $updateValues = [
                    'perms_groupid' => $siteData->getBeGroupId()
                ];
                $pagesRepository->updatePage($sitePid, $updateValues);
                $pages[] = $sitePid;
            }
            $this->log(LogLevel::INFO, 'BE Group #' . $siteData->getBeGroupId() . ' sets to pages');
            $siteData->addMessage($this->translate('generate.success.beGroupSetToPages', [$siteData->getBeGroupId(), implode(',', $pages)]));
        }
    }

}
