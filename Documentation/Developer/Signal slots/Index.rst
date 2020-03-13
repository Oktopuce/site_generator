.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _SignalSlots:

============
Signal Slots
============

The are two signals used in the extension, those signals are used to assign data to the Fluid Template forms.

+-----------------------------+---------------------------------+
| Signal                      |  Description                    |
+=============================+=================================+
| addFirstStepViewVariables   | Assign data to first form       |
+-----------------------------+---------------------------------+
| addSecondStepViewVariables  | Assign data to second form      |
+-----------------------------+---------------------------------+

ext_localconf.php
=================

Register signal slots as follow ...

.. code-block:: php

   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

   $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
   $signalSlotDispatcher->connect(
      \Oktopuce\SiteGenerator\Controller\SiteGeneratorController::class,
      'addFirstStepViewVariables',
      \Oktopuce\SiteGeneratorCustomized\Slot\SiteGeneratorSlot::class,
      'addFirstStepViewVariables'
   );

SiteGeneratorSlot.php
=====================

... and assign needed variables.

.. code-block:: php

   namespace Oktopuce\SiteGeneratorCustomized\Slot;

   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Extbase\Object\ObjectManager;
   use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
   use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

   class SiteGeneratorSlot
   {

       /**
        * Add more variable for first step view
        *
        * @param array &$viewVariables The variables array with already assigned variables
        *
        * @return void
        */
       public function addFirstStepViewVariables(array &$viewVariables): void
       {
           /** @var ObjectManager $objectManager */
           /** @var FrontendUserRepository $frontendUserRepository */
           /** @var Typo3QuerySettings $typo3QuerySettings */
           $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
           $frontendUserRepository = $objectManager->get(FrontendUserRepository::class);
           $typo3QuerySettings = $objectManager->get(Typo3QuerySettings::class);

           $typo3QuerySettings->setRespectStoragePage(false);
           $frontendUserRepository->setDefaultOrderings([
               'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
           ]);
           $frontendUserRepository->setDefaultQuerySettings($typo3QuerySettings);

           $feUsers = $frontendUserRepository->findAll();

           $viewVariables['feUsers'] = $feUsers;
       }

   }

In this example, we assigned *feUsers* variable to first Fluid Template form.
