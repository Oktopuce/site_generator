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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Log\LogLevel;
use Oktopuce\SiteGenerator\Dto\SiteGeneratorDto;

/**
 * StateFirstPageCreation
 */
class StateSetDomain extends StateBase implements SiteGeneratorStateInterface
{

    /**
     * Create domain name
     *
     * @param SiteGeneratorWizard $context
     * @return void
     */
    public function process(SiteGeneratorWizard $context)
    {
        // Create the domain name on first page
        $this->createDomain($context->getSiteData());
    }

    /**
     * Create a doamin
     *
     * @param SiteGeneratorDto $siteData New site data
     * @throws \Exception
     * @return int The id of the domain created
     */
    protected function createDomain(SiteGeneratorDto $siteData): int
    {
        $domainUid = 0;
        
        if (!empty($siteData->getDomain())) {
            /* Create a doamin */
            $data = [];
            $newUniqueId = 'NEW' . uniqid();
            $data['sys_domain'][$newUniqueId] = [
                'domainName' => $siteData->getDomain(),
                'pid' => $siteData->getHpPid()
            ];

            /* @var $dataHandler DataHandler */
            $tce = GeneralUtility::makeInstance(DataHandler::class);
            $tce->stripslashes_values = 0;
            $tce->start($data, []);
            $tce->process_datamap();

            // Retrieve uid of new page created
            $domainUid = $tce->substNEWwithIDs[$newUniqueId];

            if ($domainUid > 0) {
                $this->log(LogLevel::NOTICE, 'Domain created (uid = ' . $domainUid);
                $siteData->addMessage($this->translate('generate.success.setDomain', [$siteData->getDomain()]));
            }
            else {
                $this->log(LogLevel::ERROR, 'Cannont create domain : ' . $siteData->getDomain());
                throw new \Exception($this->translate('wizard.createDomain.error'));
            }
        }

        return ($domainUid);
    }

}
