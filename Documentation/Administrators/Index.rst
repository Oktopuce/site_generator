.. include:: /Includes.rst.txt

.. _configuration:

==================
For administrators
==================

Target group: **Developers, Integrators**

Minimal setup
===============

#. Install the extension
#. Create your model and a root page where sites can be generated
#. Within Extension Manager configuration, set 'Models Pid' and 'Sites Pid'
#. If FE group creation is needed :

   - Create a folder page for FE group creation
   - Set FE group folder pid in TypoScript Constants : pidFeGroup

#. Then you can call the wizard on 'Sites Pid' pages

Set pidFeGroup in TypoScript Constants :

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

If you only need Tree Duplication, you can change TypoScript Setup like this :

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

.. Tip::

   If you want to use the module for non-admin users, you have to allow it like any other TYPO3 modules.

   If applicable, in the "Access Rights" panel for **user** or for **user group**, check the "Web > Site generator / tree model duplicator [tx_wizard_sitegenerator]".

.. Tip::

   Before version **3.1** module TypoScript configuration like :typoscript:`module.tx_sitegenerator` can only be changed through custom extension.

   Since version **3.1** TypoScript configuration (setup only) can also be overridden with :ref:`pagets`.

Configuration
-------------

.. only:: html

   Those chapters describes how the extension can be configured

.. toctree::
   :maxdepth: 5
   :titlesonly:

   TypoScript/Index
   ExtensionManager/Index
   SiteConfiguration/Index
   UpdateTemplate/Index
   TSConfig/Index
