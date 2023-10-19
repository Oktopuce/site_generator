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
    // @TODO : ajouter les pages de type folder
    if (table === 'pages') {
      top.TYPO3.Backend.ContentContainer.setUrl(
          dataset.actionUrl  + '&action=get_data_first_step&id=' + uid + '&tx_sitegenerator[pid]=' + uid + '&returnurl=' + ContextMenuActions.getReturnUrl()
      );
    }
  };
}

export default new ContextMenuActions();
