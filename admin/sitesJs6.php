<script>
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Refactored for single-site deployment (PHP 8.3).
 * "Add New" and "Merge Sites" removed — only Update/Delete remain.
 */
"use strict";

class Sit extends Admin {
    constructor () {
      var url = '../admin/adminSrvr.php',
        form = $('#editForm'),
        dbAlias = 'sites';
      var hdrs = {'listHdr':<?php echo '"'.T("List of Sites").'"'; ?>,
            'editHdr':<?php echo '"'.T("Edit Site").'"'; ?>,
            'newHdr':<?php echo '"'.T("Add New Site").'"'; ?>,
            };
      var listFlds = {'name': 'text',
              'code': 'text',
              'city':'text',
               };
      var opts = { 'focusFld':'name', 'keyFld':'siteid' };

      super( url, form, dbAlias, hdrs, listFlds, opts );

      this.noshows = [];

    };

  async fetchList() {
    // using 'promise' technique to insure calls are processed in-turn
    await list.getSiteHoldings();
    await super.fetchList();

    await list.getPullDownList('Calendar', $('#calendar'));
  };

    fetchHandler (dataAray) {
    super.fetchHandler(dataAray);

    var $rows = $('#showList tbody tr');

    // add holdings to each site display
    $rows.each(function (i){
      var siteid = $(this).find('input[type="hidden"]').val();
      let nmbr = list.holdings[siteid];
      let html = '<td>'+nmbr+'</td>';
      $(this).append(html);
    });
  };

    // ---- Override: disable Add New completely ----
    doNewFields () {
    // no-op: single-site mode does not allow adding new sites
    return false;
    };

    // ---- Override: remove Add button from submit handling ----
    doSubmitFields (e) {
      var theBtn = e.target.id;

      if (theBtn == 'addBtn') {
        // silently ignore — Add button no longer exists in the DOM
        return false;
      }

      super.doSubmitFields(e);
    };

    // ---- Override: hide Add/Delete in edit mode (single-site: keep one site) ----
    showFields (rec) {
        $('#fieldsHdr').html(this.editHdr);
        $('#addBtn').hide();
        $('#updtBtn').show().enable();
        $('#deltBtn').show().enable();

      $('#editTbl').find('input:not(:button):not(:submit):not(:password), textarea, select').each(function () {
      var $this = $(this);

        if ($this.is('[type=radio]')) {
          $this.val([rec[this.name]]);
        } else {
        $this.val([rec[this.id]]);
        }

            var theClass = $this.get(0).className;
            if (theClass == 'addOnly') {
                $this.attr('readOnly',true);
            }
      });

      for (var n in this.noshows){
        $('#'+this.noshows[n]).attr('required',false).hide();
      };

      $('#codeReqd').hide();
      $('#listDiv').hide();
      $('#editDiv').show();
    };
}

$(document).ready(function () {
  var xxxx = new Sit();
});
</script>