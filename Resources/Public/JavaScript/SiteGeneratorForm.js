/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/SiteGenerator/SiteGeneratorForm
 */
define(['jquery',
  'TYPO3/CMS/Backend/Modal',
  'TYPO3/CMS/Backend/Severity'
], function ($, Modal, Severity) {

  /**
   * @type {Object}
   * @exports TYPO3/CMS/SiteGenerator/SiteGeneratorForm
   */
  var SiteGeneratorForm = {
  };

  /* Initialize events */
  SiteGeneratorForm.initializeEvents = function () {

    // Hide spinner
    $(".spinner").hide();

    // Submit form (same id is automatically generated for mandatory and optional form)
    $('#SiteGeneratorController').submit(function () {

      // Check mandatory field (i.e. field with class "required")
      error = false;
      $(this).find(':input').each(function () {
        if ($(this).hasClass('required') === true) {
          if (!$.trim($(this).val())) {
            error = true;
          }
        }
      });
      if (error) {
        // Display modal for error
        var $modal = Modal.confirm(TYPO3.lang['alert'], TYPO3.lang['mandatory_fields'], Severity.error, [
          {
            text: TYPO3.lang['ok'],
            active: true,
            btnClass: 'btn-default',
            name: 'ok'
          }
        ]);
        $modal.on('button.clicked', function (e) {
          Modal.dismiss();
        });
        return false;
      } else {
        // Hide show spinner
        $(".spinner").show();
        return true;
      }
    });
  };

  $(function () {
    SiteGeneratorForm.initializeEvents();
  });

  if (typeof TYPO3.SiteGeneratorForm === 'undefined') {
    TYPO3.SiteGeneratorForm = SiteGeneratorForm;
  }

  return SiteGeneratorForm;
});
