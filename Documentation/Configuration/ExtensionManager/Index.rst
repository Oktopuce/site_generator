.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _extensionManager:

Extension Manager
-----------------

Some general settings can be configured in the Extension Manager.
If you need to configure those, switch to the module "Settings > Extension Configuration" and select the extension "**site_generator**".

The settings are divided into several tabs and described here in detail:

Properties
^^^^^^^^^^

.. container:: ts-properties

	==================================== ===================================== ====================
	Property                             Tab                                   Default
	==================================== ===================================== ====================
	onlyOneFormPage                       basic                                 false
	commonMountPointUid                   basic
	supervisorGroupUid                    basic
	modelsPid                             basic
	sitesPid                              basic
	homePageTitle   	                  label                                 Home
	groupPrefix                           label                                 Group
	baseFolderName                        folder                                Website
	subFolderNames                        folder                                documents, images
	==================================== ===================================== ====================

Property details
^^^^^^^^^^^^^^^^

.. only:: html

   .. contents::
        :local:
        :depth: 1

.. _extensionManagerOnlyOneFormPage:

onlyOneFormPage
"""""""""""""""
By default there are two configuration pages to set wizard data. Set this to true if you need only one configuration page.

.. _extensionManagerCommonMountPointUid:

commonMountPointUid
"""""""""""""""""""
Here you can set the uid of a commun mount point for all sites, this mount point will then be available for each duplicated tree.

.. _extensionManageSupervisorGroupUid:

supervisorGroupUid
""""""""""""""""""
This is the uid of the supervisor group

.. _extensionManagerModelsPid:

modelsPid
"""""""""
Models page uids used for news site generation.
You can have several model pids separated with comma.

.. _extensionManagerSitesPid:

sitesPid
""""""""
Pages uid where sites can be created, use comma for multiple values.

.. _extensionManagerHomePageTitle:

homePageTitle
"""""""""""""
Define here the title used for the home page of each subsite - can be overidden in wizard form.

.. _extensionManagerGroupPrefix:

groupPrefix
"""""""""""
If set when a new group is created, the group title will be prepended with this string - can be overidden in wizard form.

.. _extensionManagerBaseFolderName:

baseFolderName
""""""""""""""
This is the name of the base folder used for each subsite mount point - can be overidden in wizard form.

.. _extensionManagerSubFolderNames:

subFolderNames
""""""""""""""
The names of the sub-folders to create inside "baseFolderName/site_title/" - comma seprated, can be overidden in wizard form.
