.. include:: /Includes.rst.txt

.. _templates:

=========
Templates
=========

There are two templates used by the wizard, the first one is used for mandatory data, the second one for optional data.
You can specified in :ref:`Extension configuration <extensionManagerOnlyOneFormPage>` that you want to use only the first template.

.. _customizeTemplates:

Customized your templates
=========================

In order to use your own templates, you have to create a new file in you extension:

:file:`<your_extension_name>Configuration/page.tsconfig`

with this content :

.. code-block:: typoscript

   templates.oktopuce/site-generator.templateRootPaths = oktopuce/site-generator:../<your_extension_name>/Resources/Private

replace **<your_extension_name>** with your own extension.


Partials/FirstStepForm.html
===========================

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
                                class="form-control form-control-adapt form-select" />
             </div>
         </div>
     </div>

.. hint::

   feUsers data are filled with :ref:`Event listener <EventListener>`.



Partials/SecondStepForm.html
============================

Second form used for optional data.
