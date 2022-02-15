.. include:: /Includes.rst.txt

.. _developer:

================
Developer Corner
================

Target group: **Developers**

The **site_generator** extension is highly customizable, this chapter describes the basics of the extension and explain how to extend it in order to fit your own needs :

- add customs states
- add specific data to forms
- change wizard steps

The site_generator wizard is based on State Design Pattern :

.. uml::

   class SiteGeneratorDto {
   }

   class StateBase {
   }

   class SiteGeneratorWizard {
      # setNextWizardState()
      __ protected data __
      #StateBase currentState
      #BaseDto siteData
   }

   class StateCopyModelSite {
      + process($context:SiteGeneratorWizard)
   }

   class StateCreateFileMount {
      + process($context:SiteGeneratorWizard)
   }

   class StateCreateFolder {
      + process($context:SiteGeneratorWizard)
   }

   class StateCreateFeGroup {
      + process($context:SiteGeneratorWizard)
   }

   SiteGeneratorDto --* SiteGeneratorWizard
   SiteGeneratorWizard o-- StateBase : currentState
   StateBase <|- StateCreateFeGroup
   StateBase <|-- StateCreateFolder
   StateBase <|-- StateCreateFileMount
   StateCopyModelSite -|> StateBase

   note left of SiteGeneratorWizard::currentState
      States comes from TypoScript Setup
   end note

   class SiteGeneratorDto
   note right: Data Transfer Object From forms wizard

The wizard get states from **TypoScript Setup** and form data through **SiteGeneraorDto**.

For full customization, I suggest to create your own extension, this is how it is suppose to be in following section, the tree structure looks like this :

.. code-block:: none

   .
   ├── Classes
   │   ├── Dto
   │   │   └── SiteGeneratorDto.php
   │   ├── EventListener
   │   │   ├── VariablesForFirstView.php
   │   │   └── VariablesForSecondView.php
   │   └── Wizard
   │       └── StateCreateFeGroup.php
   ├── Configuration
   │   └── Services.yml
   ├── composer.json
   ├── ext_emconf.php
   ├── ext_localconf.php
   ├── ext_typoscript_constants.typoscript
   ├── ext_typoscript_setup.typoscript
   └── Resources
       └── Private
           └── Templates
               └── SiteGenerator
                   ├── GetDataFirstStep.html
                   └── GetDataSecondStep.html

File details
============

.. toctree::
   :maxdepth: 3
   :titlesonly:
   :glob:

   Typoscript/Index
   Dto/Index
   EventListener/Index
   Templates/Index
   Wizard/Index
