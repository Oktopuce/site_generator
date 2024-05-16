
.. _upgrade-v2:

=====================
Upgrade to version V3
=====================

Update Fluid Template
=====================

Description
-----------

Backend view are now created with :php:`moduleTemplateFactory()` method instead of :php:`StandaloneView` and use of JavaScript ES6 modules instead of AMD modules.

Impact
-----------

Template paths are no longer initialized from TypoScript but from Page TsConfig and Fluid Templates need some changes in content and path.

Migration
-----------

Create a file :file:`<your_extension_name>Configuration/page.tsconfig` with this content :

.. code-block:: typoscript

   templates.oktopuce/site-generator.templateRootPaths = oktopuce/site-generator:../<your_extension_name>/Resources/Private

replace **<your_extension_name>** with your own extension.

remove **templateRootPath**, **partialRootPath** and **layoutRootPath** path from: :file:`<your_extension_name>/ext_typoscript_constants.typoscript` (they are no more needed).

and move your custom templates from :file:`<your_extension_name>/Resources/Private/Templates/SiteGenerator` to :file:`<your_extension_name>/Resources/Private/Templates`

**In your customized Fluid Templates change:**

.. code-block:: html

   <f:layout name="Default" />

   ...

   <f:section name="content">
      ...
   </f:section>

with:

.. code-block:: html

   <f:layout name="Module" />

   <f:section name="Before">
       <f:be.pageRenderer includeJavaScriptModules="{
           0: '@typo3/backend/context-menu.js',
           1: '@oktopuce/site-generator/site-generator-form.js'
       }"/>
   </f:section>

   ...

   <f:section name="Content">
      ...
   </f:section>

.. caution::

   Take care of the uppercase at the beginning of **"Content"** in <f:section name="Content">

.. hint::

   You can now also :ref:`use a partial <customizeTemplates>` for content form data.

Use the wizard for non-admin users
==================================

Description
-----------

Since TYPO3 version 12 the way to call the wizard from page tree has been modified and need some access rights for non-admin users.

Impact
-----------

If you don't add access rights for the module you will have a message "No module access".

Migration
-----------

In the "Access Rights" panel for **user** or for **user group**, check the "Web > Site generator / tree model duplicator [tx_wizard_sitegenerator]" to allow this module for user.

