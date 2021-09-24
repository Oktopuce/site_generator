.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _Dto:

====================
Data Transfer Object
====================

In the DTO we define all data required in the forms, the wizard will automatically affect form's data to the DTO.

.. code-block:: php

   declare(strict_types=1);

   namespace Oktopuce\SiteGeneratorCustomized\Dto;

   class SiteGeneratorDto extends \Oktopuce\SiteGenerator\Dto\SiteGeneratorDto
   {

      /**
      * My customized data from form
      *
      * @var string
      */
      protected $customizedData = '';

      /**
      * FE User
      *
      * @var int
      */
      protected $feUser = 0;

      /**
      * CustomizedData
      *
      * @param string $customizedData
      * @return void
      */
      public function setCustomizedData(string $customizedData): void
      {
         $this->customizedData = $customizedData;
      }

      /**
      * Get customizedData
      *
      * @return string
      */
      public function getCustomizedData(): string
      {
         return $this->customizedData;
      }

      /**
      * FeUser
      *
      * @param int $feUser
      * @return void
      */
      public function setFeUser(int $feUser): void
      {
         $this->feUser = $feUser;
      }

      /**
      * Get feUser
      *
      * @return int
      */
      public function getFeUser(): int
      {
         return $this->feUser;
      }
   }

.. important::

   Take care of the naming between DTO and forms.

DTO can extend class SiteGeneratorDto or BaseDto :

.. code-block:: php

   class SiteGeneratorDto extends \Oktopuce\SiteGenerator\Dto\SiteGeneratorDto

.. code-block:: php

   class SiteGeneratorDto extends \Oktopuce\SiteGenerator\Dto\BaseDto
