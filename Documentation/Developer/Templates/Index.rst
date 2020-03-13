.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _templates:

=========
Templates
=========

There are two templates used by the wizard, the first one is used for mandatory data, the second one for optional data.
You can specified in :ref:`Extension configuration <extensionManagerOnlyOneFormPage>` that you want to use only the first template.


GetDataFirstStep.php
====================

First form used for mandatory data, copy/paste Fluid Template from **site_generator** extension and add your custom data. Here is a sample with custom data added in our :ref:`DTO <dto>` : **customizedData** & **feUser**

.. code-block:: html

   <div class="row">
       <div class="form-group col-xs-12 col-md-4">
           <label class="t3js-formengine-label">Cusomized data *</label>
           <div class="form-control-clearable">
               <f:form.textfield property="customizedData" value="{siteDto.customizedData}" required="1"
                                 class="form-control t3js-clearable hasDefaultValue t3js-charcounter-initialized" />
           </div>
       </div>
   </div>
   <div class="row">
       <div class="form-group col-xs-12 col-md-6">
           <label class="t3js-formengine-label">Select FE user *</label>
           <div class="form-control-clearable">
               <f:form.select property="feUser" value="{siteDto.feUser}" options="{feUsers}"
                              optionValueField="uid" optionLabelField="name"
                              class="form-control form-control-adapt" />
           </div>
       </div>
   </div>

.. hint::

   feUsers data are filled with :ref:`Signal <SignalSlots>`.



GetDataSecondStep.php
=====================

Second form used for optional data.

.. hint::

   Take care of those two following lines in second step form, **siteDtoSaved** is used to keep data from first step form.

   .. code-block:: html

      <f:form.hidden name="siteDtoSaved" value="{siteDtoSaved}" />

   .. code-block:: html

      <f:link.action additionalParams="{action: 'get_data_first_step', siteDtoSaved: siteDtoSaved}" class="btn btn-default"><f:translate key="form.previousStep"/></f:link.action>
