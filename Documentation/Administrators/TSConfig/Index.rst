.. include:: /Includes.rst.txt

.. _pagets:

Page TSConfig
=============

Since version **3.1** TypoScript configuration (setup only) can also be overridden with Page TSConfig.

The configuration set with Page TSConfig will be merged with default configuration and default values will be overridden.

Sample
------

.. code-block:: typoscript

   module.tx_sitegenerator {
       settings {
           siteGenerator {
               wizard {
                    # Remove all steps
                    steps.clear = 1
                    steps {
                       10 = Oktopuce\SiteGenerator\Wizard\StateCopyModelSite
                       20 = Oktopuce\SiteGenerator\Wizard\StateUpdateHomePage
                       30 = Oktopuce\SiteGenerator\Wizard\StateUpdateTemplateHP
                       40 = Oktopuce\SiteGenerator\Wizard\StateUpdatePageTs
                       50 = Oktopuce\SiteGenerator\Wizard\StateUpdateSlugs
                   }
                   formDto = Oktopuce\SiteGeneratorCustomized\Dto\SiteGeneratorDto
               }
           }
       }
   }

.. Tip::
   Note the use of **steps.clear** to remove all step already defined through TypoScript

You can also override the backend templates with Page TSConfig :

.. code-block:: typoscript

   templates.oktopuce/site-generator.templateRootPaths = oktopuce/site-generator:../site_generator_customized/Resources/Private/OtherTemplates
