<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. 
 */
// JavaScript Document
"use strict";

const serviceSelect = document.getElementById('service');

function updateHostServiceFields() {
    const service = document.getElementById('service');
    const sruNote = document.getElementById('sruConfigNote');
    if (!service) {
        return;
    }
    const isSru = service.value.trim().toUpperCase() === 'SRU';
    document.querySelectorAll('[data-yaz-only="1"]').forEach(function(field) {
        field.readOnly = isSru;
        field.classList.toggle('sru-managed-field', isSru);
        field.setAttribute('aria-readonly', isSru ? 'true' : 'false');
    });
    if (sruNote) {
        sruNote.hidden = !isSru;
    }
}

class Hed extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
    			form = $('#editForm'),
    			dbAlias = 'hosts';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of Hosts").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit Hosts").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add New Host").'"'; ?>,
    				};
    	var listFlds = {'id':'text',
				'seq':'number',
    					'active':'center',
    					'host':'text',
    					'name':'text',
    					'db':'text',
    					'user':'text',
    					'pw':'password',
    					};
    	var opts = { 'focusFld':'name', 'keyFld':'id' };

	    super ( url, form, dbAlias, hdrs, listFlds, opts );

		this.fetchServiceList();
    };

    fetchServiceList () {
		$.post(this.url, {'cat':'hosts', 'mode':'getSvcs_hosts'}, $.proxy(this.serviceHandler,this));
    };
    serviceHandler (data) {
    	var html = '',
			svcs = JSON.parse(data);
    	for (var n in svcs) {
    		html += '<option value="'+svcs[n]+'">'+svcs[n]+'</option>\n';
	    }
	    $('#service').html(html);
    };
}

$(document).ready(function () {
	var hed = new Hed();
});

if (serviceSelect) {
    serviceSelect.addEventListener('change', updateHostServiceFields);
    updateHostServiceFields();
}

</script>
