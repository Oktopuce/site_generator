.. include:: /Includes.rst.txt

=====
Model
=====

.. _homePageTs:

The site generator wizard is based on models, models are dedicated parts of TYPO3 tree with pages, content elements, plugins, templates.

Update TypoScript constant in Home Page template
================================================

The model home page template could have many TypoScript constants defined for pages, tt_content, custom tables, etc.

You can add some comments in the model constant template in order to give some directives for the mapping between old uid and new uid.

Sample
------

.. code-block:: typoscript

   plugin.tx_myplugin {
     settings {
         # Nothing specified : assume it's a page ID
         pidFeGroup = 12

         # Exclude the following line from mapping : works for all tables
         # ext=SiteGenerator; action=exclude
         storageUid = 69

         # Search for 'tt_content' in MappingArray
         # ext=SiteGenerator; table=tt_content
         someContentElements = 29,30

         # Map all values in the string and exclude some of them
         # ext=SiteGenerator; table=tt_content; action=mapInString; ignoreUids=29,30
         multipleCeWithIgnore = addInList(28,29,30)

         # For custom action you must used the event UpdateTemplateHPEvent
         # ext=SiteGenerator; action=customAction; parameters=custom parameters
         forACustomAction = 515,516
     }
   }


The available directives are :

.. confval:: ext

   :Required: true
   :Description: Must be set in order to get directives
   :Sample: ext=SiteGenerator


.. t3-field-list-table::
   :header-rows: 1

   -  :Directive:    **Directive**
      :Description:  **Description**
      :Sample:       **Sample**
      :Mandatory:    **Mandatory**

   -  :Directive:    ext
      :Description:  Must be set in order to get other directives
      :Sample:       ext=SiteGenerator
      :Mandatory:    Yes

   -  :Directive:    table
      :Description:  The table name used for the mapping, if not set default is 'tables'
      :Sample:       table=tt_content
      :Mandatory:    No

   -  :Directive:    action
      :Description:  The action, exclude = exclude the row | mapInList : map values in a list like 1,2,3 | mapInString map values in a string like addList(1,2,3)
      :Sample:       action=exclude
      :Mandatory:    No

   -  :Directive:    ignoreUids
      :Description:  List of uids to ignore - comma separated
      :Sample:       ignoreUids=777,888
      :Mandatory:    No

.. tip::

   If nothing is set in comment directives, assuming that the uid is a page ID
