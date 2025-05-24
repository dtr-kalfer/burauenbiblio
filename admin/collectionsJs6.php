<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Col extends Admin {
    constructor ( ) {
	   var url = '../admin/adminSrvr.php',
	       form = $('#editForm'),
	       dbAlias = 'collect',
    	   hdrs = {'listHdr':<?php echo '"'.T("List of Collections").'"'; ?>,
			       'editHdr':<?php echo '"'.T("View Collection").'"'; ?>,
			       'newHdr':<?php echo '"'.T("Add New Collection").'"'; ?>,
		          },
	       listFlds = {'code': 'number',
				        'description':'text',
				        'type':'text',
				        'count':'number',
				        'default_flg':'center',
			         },
	       opts = {};

		super( url, form, dbAlias, hdrs, listFlds, opts );
		this.noshows = [];

		this.fetchTypes();

		list.getDueDateCalculatorList($('#due_date_calculator'));
		list.getImportantDatePurposeList($('#important_date_purpose'));

        $('#type').on('change',null,$.proxy(function () {
      	     this.setTypeDisplay();
        },this));
		$('#due_date_calculator').on('change',null,$.proxy(function () {
			this.getRelevantCalculatorFields();
		}, this));
    };

    setTypeDisplay () {
    	var type = $('#type').val();
    	if (type == 'Circulated') {
    		$('.distOnly').hide().removeAttr('required');
    		$('.circOnly').show().attr('required','true');
			this.getRelevantCalculatorFields();
    	}
    	else if (type == 'Distributed') {
    		$('.circOnly').hide().removeAttr('required');
    		$('.distOnly').show().attr('required','true');
    	}
    	else {
    		$('#userMsg').html('Invalid Collection Type');
    		$('#msgDiv').show();
    	}
    };

    getRelevantCalculatorFields () {
		$("li[class^='calculator-']").show();
		//$(".calculator-"+$("#due_date_calculator").val()).show();
    };

    fetchTypes () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getType_collect' }, $.proxy(this.typeHandler,this), 'json');
    };
    typeHandler (data){
		var html = '';
		for (var item in data) {
			//console.log(data[item]);
    	   html += '<option value="'+item+'"';
   		   html += '">'+item+'</option>\n';
		}

		$('#type').html(html);
    	this.fetchCircList();
    };

    fetchCircList () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getCirc_collect' }, $.proxy(this.circHandler,this), 'json');
    };
    circHandler (data){
        this.circList = data;
        this.fetchDistList();
    };
    getCirc (code) {
    	for (var item in this.circList) {
    		if (this.circList[item]['code'] == code) {
    			return this.circList[item];
    		}
    	}
    };

    fetchDistList () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getDist_collect' }, $.proxy(this.distHandler,this), 'json');
    };
    distHandler (data){
      	this.distList = data;
    };
    getDist(code) {
    	for (var item in this.distList) {
    		if (this.distList[item]['code'] == code) {
    			return this.distList[item];
    		}
    	}
    };

    doEditFields(e) {
    	var lclThis = this;
    	super.doEditFields.apply( this, [e] );
        lclThis.setTypeDisplay();
    	var circ = this.getCirc(this.crnt);
    	if (circ) {
    		$('#days_due_back').val(circ['days_due_back']);
    		$('#minutes_due_back').val(circ['minutes_due_back']);
    		$('#regular_late_fee').val(circ['regular_late_fee']);
		$('#due_date_calculator').val(circ['due_date_calculator']);
		$('#important_date').val(circ['important_date']);
		$('#important_date_purpose').val(circ['important_date_purpose']);
		$('#number_of_minutes_between_fee_applications').val(circ['number_of_minutes_between_fee_applications']);
		$('#pre_closing_padding').val(circ['pre_closing_padding']);
    	}
    	var dist = this.getDist(this.crnt);
    	if (dist) {
    		$('#restock_threshold').val(dist['restock_threshold']);
    	}
    };
}

$(document).ready(function () {
    var xxxx = new Col;
});

//---------- added dynnamic regex filter for input borrow duration policy -----
function allowOnlyDigits(id, maxLength) {
    const input = document.getElementById(id);
    input.addEventListener("input", function () {
        // Remove all non-digit characters
        this.value = this.value.replace(/\D/g, '').slice(0, maxLength);
    });
}

function allowLateFeeFormat(id) {
    const input = document.getElementById(id);
    input.addEventListener("input", function () {
        // Allow only numbers with 1 or 2 digits before decimal and exactly 2 after
        const match = this.value.match(/^(\d{0,2})(\.?\d{0,2})/);
        this.value = match ? match[1] + match[2] : '';
    });
}

// Allow only digits, limit to exact length
allowOnlyDigits("rpt_c_code", 2);              // e.g. 10–99
allowOnlyDigits("rpt_c_days_due_back", 3);     // e.g. 100–999

// Allow only late fee values like 0.01 to 9.99 format
allowLateFeeFormat("rpt_c_regular_late_fee");
</script>