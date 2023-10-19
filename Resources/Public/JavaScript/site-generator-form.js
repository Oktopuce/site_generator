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
 * Module: @oktopuce/site-generator/site-generator-form.js
 */
// @TODO : remove jQuery
import $ from 'jquery';
import Modal from '@typo3/backend/modal.js';
import Severity from '@typo3/backend/severity.js';

class SiteGeneratorForm {
    constructor() {
        this.initializeEvents()
    }

    initializeEvents() {
        // Hide spinner
        $(".spinner").hide();

        // Submit form (same id is automatically generated for mandatory and optional form)
        $('#SiteGeneratorController').submit(function () {

            // Check mandatory field (i.e. field with class "required")
            let error = false;
            $(this).find(':input').each(function () {
                if ($(this).hasClass('required') === true) {
                    if (!$.trim($(this).val())) {
                        error = true;
                    }
                }
            });

            if (error) {
                // Display modal for error
                Modal.confirm(TYPO3.lang['alert'], TYPO3.lang['mandatory_fields'], Severity.warning, [
                    {
                        text: TYPO3.lang['ok'],
                        active: true,
                        trigger: function () {
                            Modal.dismiss();
                        }
                    }
                ]);
                return false;
            } else {
                // Show spinner
                $(".spinner").show();
                return true;
            }
        });
    }
}

export default new SiteGeneratorForm;
