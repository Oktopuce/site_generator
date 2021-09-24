.. include:: ../../../Includes.txt


.. _upgrade-v2:

=====================
Upgrade to version V2
=====================

.. hint::
   If you don't use "signal/slot" within previous version and have no wizard custom state, you can upgrade to new version without doing any change.

Use type hint with wizard custom state
======================================

Description
-----------

The :php:`SiteGeneratorStateInterface()` signature have changed to use type hint.

Impact
-----------

A PHP error will be thrown if you don't update the custom steps of your extension.

Migration
-----------

Just change the :php:`process()` method signature of your custom steps from :

.. code-block:: php

   public function process(SiteGeneratorWizard $context)

to :

.. code-block:: php

   public function process(SiteGeneratorWizard $context): void


Replace signal/slot with PSR-14 events
======================================

Description
-----------

Within the File Abstraction Layer, all "Signals" of Extbase's SignalSlot dispatcher have been migrated to PSR-14 events.

For this reason, all FAL-related Signals have been migrated to PSR-14 event listeners which are prioritized as the
first listener to be executed when an Event is fired.

More informations at `Deprecation: #89577 - FAL SignalSlot handling migrated to PSR-14 events <https://docs.typo3.org/c/typo3/cms-core/11.1/en-us/Changelog/10.2/Deprecation-89577-FALSignalSlotHandlingMigratedToPSR-14Events.html>`__.

Impact
-----------

Signal slot used in previous version have been removed and won't work anymore.

Migration
-----------

Use new PSR-14 events as a replacement for old signal/slot.

Remove signal slot dispatchers registered in :file:`ext_localconf.php`.

Replace your old slots with new event listener as described in :ref:`Event listener <EventListener>`.




