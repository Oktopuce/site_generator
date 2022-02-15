.. include:: /Includes.rst.txt

.. _EventListener:

================================
Event listener (PSR-14 Events)
================================

The are three event dispatchers used in the extension, two of them are used to assign data to the Fluid Template forms and the
third one is used to customize mapping of home page constants with custom directives.

+-------------------------------------+-------------------------------------------------------+
| Event                               |  Description                                          |
+=====================================+=======================================================+
| BeforeRenderingFirstStepViewEvent   | Assign data to first form                             |
+-------------------------------------+-------------------------------------------------------+
| BeforeRenderingSecondStepViewEvent  | Assign data to second form                            |
+-------------------------------------+-------------------------------------------------------+
| UpdateTemplateHPEvent               | Custom directives for constants in home page template |
+-------------------------------------+-------------------------------------------------------+

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

      Oktopuce\SiteGeneratorCustomized\EventListener\UpdateTemplateHP:
         tags:
            - name: event.listener
              identifier: 'updateTemplateHP'
              event: Oktopuce\SiteGenerator\Wizard\Event\UpdateTemplateHPEvent

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

Custom directives for constants in home page template
-----------------------------------------------------

Sample customize directives for TypoScript constants mapping with some TypoScript directives in home page model template.

TypoScript constants in home page model template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: typoscript

   plugin.tx_myplugin {
     settings {
         # ext=SiteGenerator; action=customAction; parameters=custom parameters
         forACustomAction = 515,516
     }
   }


Classes/EventListener/UpdateTemplateHP.php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

   declare(strict_types=1);

   namespace Oktopuce\SiteGeneratorCustomized\EventListener;

   use Oktopuce\SiteGenerator\Wizard\Event\UpdateTemplateHPEvent;

   class UpdateTemplateHP
   {
       /**
        * __invoke
        *
        * @param UpdateTemplateHPEvent $event
        * @return void
        */
       public function __invoke(UpdateTemplateHPEvent $event): void
       {
           $action = $event->getAction();
           if ($action == 'customAction') {
               $parameters = $event->getParameters();
               $value = $event->getValue();
               $dataMapping = $event->getFilteredMapping();
               $updatedValue = "params = $parameters - value = $value - dataMapping = " . implode(',', $dataMapping);

               $event->setUpdatedValue($updatedValue);
           }
       }
   }

.. tip::

   Have a look at `TYPO3 documentation <https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Hooks/EventDispatcher/Index.html#implementing-an-event-listener-in-your-extension>`__ for more information on event listener.
