.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _extensionManager:

Extension Manager
-----------------

Some general settings can be configured in the Extension Manager.
If you need to configure those, switch to the module :guilabel:`Settings > Extension Configuration` and select the extension "**site_generator**".

.. Important::
   **modelsPid** and **sitesPid** are mandatory fields.

The settings are divided into several tabs and described here in detail:

Properties
^^^^^^^^^^

.. container:: ts-properties

   ==================================== ===================================== ====================
   Property                             Tab                                   Default
   ==================================== ===================================== ====================
   onlyOneFormPage_                      basic                                 false
   commonMountPointUid_                  basic
   modelsPid_                            basic
   sitesPid_                             basic
   homePageTitle_                        label                                 Home
   groupPrefix_                          label                                 Group
   baseFolderName_                       folder                                Website
   subFolderNames_                       folder                                documents, images
   groupMods_                            access lists                          web_layout,web_ViewpageView,web_list,file_FilelistList,user_setup
   tablesSelect_                         access lists                          pages,sys_file,sys_file_metadata,sys_file_reference,tt_content
   tablesModify_                          access lists                          pages,sys_file,sys_file_metadata,sys_file_reference,tt_content
   explicitAllowdeny_                    access lists                          tt_content:CType:media:ALLOW,tt_content:CType:textteaser:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:CType:image:ALLOW,tt_content:CType:textmedia:ALLOW
   siteIdentifierPrefix_                 site configuration                    siteGenerator-
   langTitle_                            site configuration                    English
   locale_                               site configuration                    en_US.UTF-8
   iso-639-1_                            site configuration                    en
   navigationTitle_                      site configuration                    English
   hreflang_                             site configuration                    en-US
   direction_                            site configuration                    ltr
   flag_                                 site configuration                    en-us-gb
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
By default there are two configuration pages - one for mandatory data and another one for optional data - to set wizard data. Set this to true if you need only one configuration page.

.. _extensionManagerCommonMountPointUid:

commonMountPointUid
"""""""""""""""""""
Here you can set the uid of a commun mount point for all sites, this mount point will then be available for each duplicated tree.

.. _extensionManagerModelsPid:

modelsPid
"""""""""
Models page uids used for news site generation.
You can have several model pids separated with comma.

.. _extensionManagerSitesPid:

sitesPid
""""""""
Pages uid where sites can be created, use comma for multiple values. The site generator wizard will only be available on that pages.

.. _extensionManagerHomePageTitle:

homePageTitle
"""""""""""""
Define here the title used for the home page of each subsite - can be overridden in wizard form.

.. _extensionManagerGroupPrefix:

groupPrefix
"""""""""""
If set when a new FE or BE group is created, the group title will be prepended with this string - can be overridden in wizard form.

.. _extensionManagerBaseFolderName:

baseFolderName
""""""""""""""
This is the name of the base folder used for each subsite mount point - can be overridden in wizard form.

.. _extensionManagerSubFolderNames:

subFolderNames
""""""""""""""
The names of the sub-folders to create inside "baseFolderName/site_title/" - comma separated, can be overridden in wizard form.

.. _extensionManagerGroupMods:

groupMods
"""""""""
List of allowed modules for BE group (comma separated)

.. _extensionManagerTablesSelect:

tablesSelect
""""""""""""
Listing of tables that can be select for BE group (comma separated)

.. _extensionManagerTablesModify:

tablesModify
""""""""""""
List of tables that can be modified for BE group (comma separated)

.. _extensionManagerExplicitAllowdeny:

explicitAllowdeny
"""""""""""""""""
Set tt_content allowed CType for BE group (comma separated)

.. _extensionManagerSiteIdentifierPrefix:

siteIdentifierPrefix
""""""""""""""""""""
The prefix used for site identifier in site configuration

.. _extensionManagerLangTitle:

langTitle
"""""""""
The language title (ex : English)

.. _extensionManagerLocale:

locale
""""""
Locale used for localized date and currency formats. E.g. "de_DE" or "en_US.UTF-8".

.. _extensionManagerIso-639-1:

iso-639-1
"""""""""
Two letters of ISO 639-1 code of the language, sample : en, fr

.. _extensionManagerNavigationTitle:

navigationTitle
"""""""""""""""
The navigation title used within language-related menus, sample : English, Français

.. _extensionManagerHreflang:

hreflang
""""""""
The language tag defined by RFC 1766 / 3066. Used within for "lang" and "hreflang" attributes, sample : en-US, fr-FR

.. _extensionManagerDirection:

direction
"""""""""
The language direction for "dir" attribute : empty (none), ltr or rtl

.. _extensionManagerFlag:

flag
""""
The language flag icon, sample : en-us-gb, fr
