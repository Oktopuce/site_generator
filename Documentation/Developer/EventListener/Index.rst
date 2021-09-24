.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _EventListener:

================================
Event listener (PSR-14 Events)
================================

The are two event dispatchers used in the extension, those events are used to assign data to the Fluid Template forms.

+-------------------------------------+---------------------------------+
| Event                               |  Description                    |
+=====================================+=================================+
| BeforeRenderingFirstStepViewEvent   | Assign data to first form       |
+-------------------------------------+---------------------------------+
| BeforeRenderingSecondStepViewEvent  | Assign data to second form      |
+-------------------------------------+---------------------------------+

Implementing an event listener in your extension
================================================

If you need to provide an event listener in your extension, you have to :

* register the listener in :file:`Configuration/Services.yaml`
* create an event listener class

Configuration/Services.yaml
---------------------------

Register event listener as follow ...

.. code-block:: php

   services:
      Oktopuce\SiteGeneratorCustomized\EventListener\VariablesForFirstView:
         tags:
            - name: event.listener
            identifier: 'customizeFirstStep'
            event: Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingFirstStepViewEvent

      Oktopuce\SiteGeneratorCustomized\EventListener\VariablesForSecondView:
         tags:
            - name: event.listener
            identifier: 'customizeSecondtStep'
            event: Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingSecondStepViewEvent

Classes/EventListener/VariablesForFirstView.php
-----------------------------------------------

... and assign needed variables in the listener with event dispatcher method :php:`addViewVariables(array $variables);`

.. code-block:: php

   declare(strict_types=1);

   namespace Oktopuce\SiteGeneratorCustomized\EventListener;

   use TYPO3\CMS\Extbase\Persistence\QueryInterface;
   use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
   use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
   use Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingFirstStepViewEvent;

   class VariablesForFirstView
   {
      /**
      * @var FrontendUserRepository
      */
      protected $frontendUserRepository = null;

      /**
      * @var Typo3QuerySettings
      */
      protected $typo3QuerySettings = null;

      /**
      * Class constructor
      *
      * @param PageRenderer $pageRenderer
      * @param Typo3QuerySettings $typo3QuerySettings
      */
      public function __construct(FrontendUserRepository $frontendUserRepository, Typo3QuerySettings $typo3QuerySettings)
      {
         $this->frontendUserRepository = $frontendUserRepository;
         $this->typo3QuerySettings = $typo3QuerySettings;
      }

      /**
      * __invoke
      *
      * @param  array $viewVariables
      * @return void
      */
      public function __invoke(BeforeRenderingFirstStepViewEvent $event): void
      {
         $this->typo3QuerySettings->setRespectStoragePage(false);
         $this->frontendUserRepository->setDefaultOrderings([
               'name' => QueryInterface::ORDER_ASCENDING
         ]);
         $this->frontendUserRepository->setDefaultQuerySettings($this->typo3QuerySettings);

         $feUsers = $this->frontendUserRepository->findAll();

         $event->addViewVariables(['feUsers' => $feUsers]);
      }
   }

In this example, we assigned *feUsers* variable to first Fluid Template form.

Have a look at `Typo3 documentation <https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Hooks/EventDispatcher/Index.html#implementing-an-event-listener-in-your-extension>`__ for more information on event listener.
