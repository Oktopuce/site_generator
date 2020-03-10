/* global TYPO3 */

/**
 * Module: TYPO3/CMS/SiteGenerator/ContextMenuActions
 *
 * JavaScript to handle the click action of the "Site Generator" context menu item
 * @exports TYPO3/CMS/SiteGenerator/ContextMenuActions
 */
define(function () {
  'use strict';

  /**
   * @exports TYPO3/CMS/SiteGenerator/ContextMenuActions
   */
  var ContextMenuActions = {};

  ContextMenuActions.getReturnUrl = function () {
    return top.rawurlencode(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
  };

  /**
   * Call site generator module
   *
   * @param {string} table
   * @param {int} uid of the page
   */
  ContextMenuActions.siteGenerator = function (table, uid) {
    if (table === 'pages') {
      // If needed, you can access other 'data' attributes here from $(this).data('someKey')
      // see item provider getAdditionalAttributes method to see how to pass custom data attributes
      TYPO3.Backend.ContentContainer.setUrl(
        TYPO3.settings.SiteGenerator.moduleUrl +
        '&action=get_data_first_step' +
        '&tx_sitegenerator[pid]=' + uid +
        '&returnurl=' + ContextMenuActions.getReturnUrl()
      );
    }
  };

  return ContextMenuActions;
});