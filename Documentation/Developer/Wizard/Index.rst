.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _wizard:

======
Wizard
======

In *Wizard* folder we add all our custom states for the wizard, in this example we add a custom state for FE user generation.

All states are declared in :ref:`Typoscript Setup <screenshots>` for the wizard to know about them.

.. code-block:: php

   declare(strict_types=1);

   namespace Oktopuce\SiteGeneratorCustomized\Wizard;

   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Core\Log\LogLevel;
   use TYPO3\CMS\Core\DataHandling\DataHandler;
   use Oktopuce\SiteGenerator\Wizard\SiteGeneratorWizard;
   use Oktopuce\SiteGenerator\Wizard\SiteGeneratorStateInterface;
   use Oktopuce\SiteGenerator\Wizard\StateBase;
   use Oktopuce\SiteGeneratorCustomized\Dto\SiteGeneratorDto;

   /**
   * StateCreateFeGroup
   */
   class StateCreateFeGroup extends StateBase implements SiteGeneratorStateInterface
   {
      /**
      * Create FE user group
      *
      * @param SiteGeneratorWizard $context
      * @return void
      */
      public function process(SiteGeneratorWizard $context): void
      {
         $settings = $context->getSettings();

         // Create FE group
         $groupId = $this->createFeGroup($context->getSiteData(), (int)$settings['siteGenerator']['wizard']['pidFeGroup'], (int)$settings['siteGenerator']['wizard']['baseFeGroupUid']);
         $context->getSiteData()->setFeGroupId($groupId);
      }

      /**
      * Create FE group
      *
      * @param SiteGeneratorDto $siteData New site data
      * @param int $pidFeGroup Pid for FE group creation
      * @param int $baseFeGroupUid Base Workgroup UID
      * @throws \Exception
      *
      * @return int The uid of the group created
      */
      protected function createFeGroup(SiteGeneratorDto $siteData, int $pidFeGroup, int $baseFeGroupUid): int
      {
         // Create a new FE group with specific subgroup
         $data = [];
         $newUniqueId = 'NEW' . uniqid();
         $groupName = $siteData->getCustomizedData() . ' - ' . $siteData->getTitle();
         $data['fe_groups'][$newUniqueId] = [
               'pid' => $pidFeGroup,
               'title' => $groupName,
               'subgroup' => $baseFeGroupUid
         ];

         /* @var $tce DataHandler */
         $tce = GeneralUtility::makeInstance(DataHandler::class);
         $tce->stripslashes_values = 0;
         $tce->start($data, []);
         $tce->process_datamap();

         // Retrieve uid of user group created
         $groupId = $tce->substNEWwithIDs[$newUniqueId];

         if ($groupId > 0) {
               $this->log(LogLevel::NOTICE, 'Create FE group successful (uid = ' . $groupId);
               $siteData->addMessage($this->translate('generate.success.feGroupCreated', [$groupName, $groupId]));
         }
         else {
               $this->log(LogLevel::ERROR, 'Create FE group error');
               throw new \Exception($this->translate('wizard.feGroup.error'));
         }

         return ($groupId);
      }
   }

.. caution::

   All states must extend **StateBase** and implements **SiteGeneratorStateInterface**.
   The process method is mandatory.

.. hint::

   You can add information to sys_log with the method **log()** :

   .. code-block:: php

      $this->log(LogLevel::NOTICE, 'My message');

   The **addMessage()** method is used to add a message that will be displayed at the end of the process.

   .. code-block:: php

      $siteData->addMessage('- My message');
