/**
 * Module: @oktopuce/sitegenerator/ContextMenuActions
 *
 * JavaScript to handle the click action of the "Site Generator" context menu item
 */

class ContextMenuActions {
  static getReturnUrl(){
    return encodeURIComponent(top.list_frame.document.location.pathname+top.list_frame.document.location.search)
  }

  siteGenerator(table, uid, dataset) {
    if (table === 'pages') {
      top.TYPO3.Backend.ContentContainer.setUrl(
          dataset.actionUrl  + '&action=getDataFirstStep&id=' + uid + '&tx_sitegenerator[pid]=' + uid + '&returnurl=' + ContextMenuActions.getReturnUrl()
      );
    }
  };
}

export default new ContextMenuActions();
