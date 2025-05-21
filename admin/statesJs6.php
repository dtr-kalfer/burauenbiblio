<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class St extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'states';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of provinces and its UAC Code").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit province & UAC Code").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add province & UAC Code").'"'; ?>,
    						 };
    	var listFlds = {'code':'text',
    					'description':'text',
    					'default_flg':'center',
    								 };
    	var opts = {};

	    super( url, form, dbAlias, hdrs, listFlds, opts );
    	this.noshows = [];
    };
}

$(document).ready(function () {
	var xxxx = new St();
});

</script>
