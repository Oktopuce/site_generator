.. include:: /Includes.rst.txt

.. _SiteConfiguration:

==================
Site configuration
==================

Since version **3.2.0** you can override all extension configuration setting - except **sitesPid** - with site configuration.

To override the configuration, you must modify the site configuration file **config.yaml** - property names are the same as :ref:`extension configuration <ExtensionManager>`.

Overriding extension configuration with site configuration
----------------------------------------------------------

Add a **siteGenerator** section with any settings that need to be overridden.

..  literalinclude:: _sitegenerator-config.yaml
    :caption: config/sites/<identifier>/config.yaml
