.. include:: ../Includes.txt


.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

Minimal setup
===============

#. Install the extension
#. Create your model and a root page where sites can be generated
#. Within Extension Manager configuration, set 'Models Pid' and 'Sites Pid'
#. If FE group creation is needed :

   - Create a folder page for FE group creation
   - Set FE group folder pid in Typoscript Constants : pidFeGroup

#. Then you can call the wizard on 'Sites Pid' pages

Set pidFeGroup in Typoscript Constants :

.. code-block:: typoscript

   module.tx_sitegenerator {
       settings {
           siteGenerator {
               wizard {
                   pidFeGroup = xxx
               }
           }
       }
   }

.. _configuration-typoscript:

If you only need Tree Duplication, you can change Typoscript Setup like this :

.. code-block:: typoscript

   module.tx_sitegenerator {
       settings {
           siteGenerator {
               wizard {
                   steps >
                   steps {
                       10 = Oktopuce\SiteGenerator\Wizard\StateCopyModelSite
                       20 = Oktopuce\SiteGenerator\Wizard\StateUpdateHomePage
                       30 = Oktopuce\SiteGenerator\Wizard\StateUpdateTemplateHP
                       40 = Oktopuce\SiteGenerator\Wizard\StateUpdatePageTs
                       50 = Oktopuce\SiteGenerator\Wizard\StateUpdateSlugs
                   }
               }
           }
       }
   }

.. _configuration-typoscript:


Configuration
-------------

.. only:: html

   Those chapters describes how the extension can be configured

.. toctree::
   :maxdepth: 5
   :titlesonly:

   TypoScript/Index
   TsConfig/Index
   ExtensionManager/Index
