.. include:: /Includes.rst.txt

.. _ts:

TypoScript
==========

Here you can find the module configuration available in TypoScript.

Any setting needs to be prefixed with  :typoscript:`module.tx_sitegenerator.settings.siteGenerator.wizard.`.

.. _formDto:

formDto
-------

.. container:: table-row

   Property
         formDto

   Data type
         string

   Default
         Oktopuce\\SiteGenerator\\Dto\\SiteGeneratorDto

   Description
         Class name for DTO (Data Transfer Object) used for data exchange between form and wizard

.. _pidFeGroup:

pidFeGroup
----------

.. container:: table-row

   Property
         pidFeGroup

   Data type
         integer

   Default
         0

   Description
         Page Id for FE group creation - should be a folder - if set to 0, no FE group will be created

.. _storageUid:

storageUid
----------

.. container:: table-row

   Property
         storageUid

   Data type
         integer

   Default
         1

   Description
         Storage uid used for folder creation (0 = no folder creation, 1 = default storage - i.e. fileadmin)

.. _hideHomePage:

hideHomePage
------------

.. container:: table-row

   Property
         hideHomePage

   Data type
         boolean

   Default
         0

   Description
         Set this to '1' if you want to set home page as hidden
