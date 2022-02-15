.. include:: /Includes.rst.txt

.. _typoscript:

==========
TypoScript
==========

Configure you extension in order to use your own forms and customize the wizard states.

.. _extTyposcriptConstants:

ext_typoscript_constants.typoscript
===================================

.. code-block:: typoscript

   module.tx_sitegenerator {
       view {
           templateRootPath = EXT:site_generator_customized/Resources/Private/Templates/
           partialRootPath = EXT:site_generator_customized/Resources/Private/Partials/
           layoutRootPath = EXT:site_generator_customized/Resources/Private/Layouts/
       }
       settings {
           siteGenerator {
               wizard {
                   // Custom form DTO
                   formDto = Oktopuce\SiteGeneratorCustomized\Dto\SiteGeneratorDto
                   // Pid for FE group creation
                   pidFeGroup = 20
                   // Base FE group UID
                   baseFeGroupUid = 6
               }
           }
       }
   }

.. important::

   Most important thing here is the use of a specific DTO that will be used with our custom forms

   .. code-block:: typoscript

      formDto = Oktopuce\SiteGeneratorCustomized\Dto\SiteGeneratorDto


.. _extTyposcriptSetup:

ext_typoscript_setup.typoscript
===============================

.. code-block:: typoscript

   # Clear all default states and set new Wizard steps
   module.tx_sitegenerator.settings.siteGenerator.wizard.steps >
   module.tx_sitegenerator {
       settings {
           siteGenerator {
               wizard {
                   steps {
                       10 = Oktopuce\SiteGenerator\Wizard\StateCopyModelSite
                       20 = Oktopuce\SiteGeneratorCustomized\Wizard\StateCreateFeGroup
                       30 = Oktopuce\SiteGenerator\Wizard\StateUpdateHomePage
                       40 = Oktopuce\SiteGenerator\Wizard\StateUpdateTemplateHP
                       50 = Oktopuce\SiteGenerator\Wizard\StateUpdatePageTs
                       60 = Oktopuce\SiteGenerator\Wizard\StateUpdateSlugs
                   }
                   baseFeGroupUid = {$module.tx_sitegenerator.settings.siteGenerator.wizard.baseFeGroupUid}
               }
           }
       }
   }

.. code-block:: typoscript

   20 = Oktopuce\SiteGeneratorCustomized\Wizard\StateCreateFeGroup

.. important::

   Reset all default wizard states, reuse existing states and add your customized state
