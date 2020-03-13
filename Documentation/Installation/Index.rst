.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _installation:

============
Installation
============

Composer based instance
-----------------------

If you base your TYPO3 instance on a modern composer based installation, just require the package via composer:

.. code-block:: bash

	composer require oktopuce/site-generator

Then go to the backend extension manager and load the extension in "Installed Extensions".

TYPO3 download based instance
-----------------------

The extension needs to be installed as any other extension of TYPO3 CMS:

#. Switch to the module “Extension Manager”.

#. Get the extension

   #. **Get it from the Extension Manager:** Press the “Retrieve/Update”
      button and search for the extension key *site_generator* and import the
      extension from the repository.

   #. **Get it from typo3.org:** You can always get current version from
      `http://typo3.org/extensions/repository/view/site_generator/current/
      <http://typo3.org/extensions/repository/view/site_generator/current/>`_ by
      downloading either the t3x or zip version. Upload
      the file afterwards in the Extension Manager.

#. The Extension Manager offers some basic configuration which is
   explained :ref:`here <extensionManager>`.

Latest version from git
-----------------------
You can get the latest version from git by using the git command:

.. code-block:: bash

   git clone https://github.com/Oktopuce/site_generator.git
