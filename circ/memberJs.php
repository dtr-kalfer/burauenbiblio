<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
$current_timestamp = time();
?>
<script language="JavaScript" defer>

// JavaScript Document
//------------------------------------------------------------------------------
"use strict";

function shortenTitle(title, maxLength) {
	if (title.length > maxLength) {
		//trim the string to the maximum length
		var trimmedString = title.substr(0, maxLength);

		//re-trim if we are in the middle of a word
		return trimmedString.substr(0, Math.min(trimmedString.length, trimmedString.lastIndexOf(" "))) + '...';
	}
	return title;
}

var mf = {
	<?php
	if ($_SESSION['mbrBarcode_flg'] == 'Y') 
		echo "showBarCd: true, \n";
	else
		echo "showBarCd: false, \n ";
	?>
	multiMode: false,
	
	init: function () {
		mf.url = '../circ/memberServer.php';
		mf.listSrvr = "../shared/listSrvr.php";

		// get header stuff going first
		mf.initWidgets();
		mf.resetForms();
		mf.fetchOpts();
		mf.fetchCustomFlds();
		mf.fetchAcnttranTypes();
		mf.prepareCopyBarcdValidation();
		mf.buildClassificationMap();						
		
		$('.gobkBtn').on('click',null,mf.rtnToSrch);
		$('.gobkMbrBtn').on('click',null,mf.rtnToList);
		$('.gobkNewBtn').on('click',null,mf.rtnToSrch);
		$('.gobkUpdtBtn').on('click',null,mf.rtnToMbr);
		$('.gobkBiblioBtn').on('click',null,mf.rtnToMbr);
		$('.gobkAcntBtn').on('click',null,mf.rtnToMbr);
		$('.gobkHistBtn').on('click',null,mf.rtnToMbr);

		$('#barCdSrchBtn').on('click',null,mf.doBarCdSearch);
		$('#nameSrchBtn').on('click',null,mf.doNameSearch);
		$('#addNewMbrBtn').on('click',null,mf.doShowMbrAdd);

		$('#addMbrBtn').on('click',null,mf.doMbrAdd);
		$('#updtMbrBtn').on('click',null,mf.doMbrUpdate);
		$('#deltMbrBtn').on('click',null,mf.doDeleteMember);
		$('#cnclMbrBtn').on('click',null,function(){
			mf.doFetchMember();
			mf.rtnToMbr();
		});

		$('#mbrDetlBtn').on('click',null,mf.doShowMbrDetails);

		$('#mbrAcntBtn').on('click',null,mf.doShowMbrAcnt);
		$('#addTransBtn').on('click',null,mf.doTransAdd);

		$('#mbrHistBtn').on('click',null,mf.doShowMbrHist);
		
		$('#mbrHelpBtn').on('click',null,mf.doShowMbrHelp);

		$('#chkOutBtn').on('click',null,mf.doCheckout);
		$('#holdBtn').on('click',null,mf.doHold);

		// prepare pull-down lists
		mf.fetchMbrTypList();
		mf.fetchSiteList();
		mf.fetchStateList();
		<?php
		if (isset($_GET['mbrid'])) { 
			echo "mf.doMbridSearch (".$_GET['mbrid'].");";
		}
		?>
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForms: function () {
	  //console.log('resetting Search Form');
		if (!mf.showBarCd) $('#barCdSrchForm').hide();
		$('p.error, input.error').html('').hide();
		$('.gobkNewBtn').hide();
		$('.gobkUpdtBtn').hide();
	  $('#searchDiv').show();
	  $('#listDiv').hide();
	  $('#mbrDiv').hide();
	  $('#biblioDiv').hide();
	  $('#newDiv').hide();
	  $('#editDiv').hide();
	  $('#acntDiv').hide();
	  $('#histDiv').hide();
		$('#msgDiv').hide();
		$('#chkOutBtn').enable();
		$('#chkOutMsg').html('').hide();
		if(mf.showBarCd) {
			$('#searchByBarcd').focus();
		} else {
			$('#nameFrag').focus();
		}
	},
	prepareCopyBarcdValidation: function (){
		$('#ckoutBarcd').attr('pattern', "[0-9]{<?php echo Settings::get('item_barcode_width');?>}" );
	},
	rtnToSrch: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
		$('#ckOutBarcd').val('')
	  mf.resetForms();
	  $('#searchDiv').show();
	},
	rtnToList: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
		$('#ckOutBarcd').val('')
	  //mf.resetForms();
		$('#chkOutBtn').enable();
		$('#chkOutMsg').html('').hide();
	  $('#mbrDiv').hide();
	  $('#listDiv').show();
		$('#msgDiv').hide();
		$('#ttlOwed').html('0');
		$('#helpDiv').hide();
	},
	rtnToMbr: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  mf.resetForms();
	  mf.doFetchMember();
	  $('#biblioDiv').hide();
		$('#helpDiv').hide();
	},

	showMsg: function (msg) {
		$('#userMsg').html(msg);
		$('#msgDiv').show();
	},

	//------------------------------
	fetchOpts: function () {
	  $.post(mf.url,{mode:'getOpts'}, function(jsonData){
	    mf.opts = jsonData
		}, 'json');
	},
	getNewBarCd: function () {
	  $.post(mf.url,{mode:'getNewBarCd', width:6}, function(data){
			$('#barcode_nmbr').val(data);
		});
	},
	fetchMbrTypList: function () {
	  $.post(mf.listSrvr,{mode:'getMbrTypList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n].description+'</option>';
			}
			$('#classification').html(html);
		}, 'json');
	},
	fetchSiteList: function () {
	  $.post(mf.listSrvr,{mode:'getSiteList'}, function(data){
			var html = '';
			mf.sites = data;
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#siteid').html(html);
		}, 'json');
	},
	fetchStateList: function () {
	  $.post(mf.listSrvr,{mode:'getStateList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n].description+'</option>';
			}
			$('#state').html(html);
		}, 'json');
	},
	fetchCustomFlds: function () {
	  $.post(mf.url,{mode:'getCustomFlds'}, function(jsonData){
			if ((jsonData.trim()).substr(0,1) == '<') {
				mf.showMsg(jsonData);
				return false;
			} else {
	  		mf.cstmFlds = JSON.parse(jsonData);
			}
		});
	},
	fetchAcnttranTypes: function () {
	  $.post(mf.url,{mode:'getAcntTranTypes'}, function(jsonData){
	  	mf.tranType = jsonData;
	    var html = '';
	    $.each(jsonData, function (name, value) {
	    	html += '<option value="'+name+'">'+value+'</option> \n';
			});
			$('#transaction_type_cd').html(html);
		}, 'json');
	},
	
	//------------------------------
	doMbridSearch: function (mbrid) {
	  mf.srchType = 'mbrid';
		mf.mbrid = mbrid;
	  var params = 'mode=doGetMbr&mbrid='+mbrid;
	  $.post(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	doFetchMember: function () {
	  var params = 'mode=doGetMbr&mbrid='+mf.mbrid;
	  $.post(mf.url,params, mf.handleMbrResponse);
		return false;
	},

	doBarCdSearch: function () { // improved: added validity check (6 to 8 digits) for empty input --F.Tumulak
		var barcdInput = $('#searchByBarcd')[0]; // get the DOM element
		var barcd = $.trim(barcdInput.value);

		// Update input value (cleaned one)
		$('#searchByBarcd').val(barcd);

		// Check if empty or invalid (manual check or HTML5 validity)
		if (!barcd || !/^\d{6,8}$/.test(barcd)) {
			barcdInput.setCustomValidity("Please enter 6 to 8 digits.");
			barcdInput.reportValidity(); // Show the browser's native tooltip
			return false; // stop the function
		} else {
			barcdInput.setCustomValidity(""); // clear any custom message
		}

		// Proceed with search
		mf.srchType = 'barCd';
		var params = 'mode=doBarcdSearch&barcdNmbr=' + barcd;
		$.post(mf.url, params, mf.handleMbrResponse_barcd);
		return false;
	},

// --------- Map function for showing member types on immediate search results -- F.  Tumulak
	buildClassificationMap: function () {
		$.post(mf.listSrvr, { mode: 'getMbrTypList' }, function (data) {
			var classificationMap = {};

			// Predefined emojis for up to 10 member types
			var emojiList = [
				'👩‍🏫', // 1 - Faculty
				'🧑‍', // 2 - Student
				'👤', // 3 - Part-time
				'🧳',   // 4 - Visitor
				'🏫', // 5 - Teacher Aide
				'📚',   // 6 - Librarian
				'👨‍💻', // 7 - IT Staff
				'🏫',   // 8 - Admin
				'🔬', // 9 - Researcher
				'👥'    // 10 - Guest/Other
			];
			
			// create the map of membertypes
			var index = 0;
			for (var n in data) {
				let emoji = emojiList[index] || '👤'; // fallback emoji if over 10
				classificationMap[n] = emoji + ' ' + data[n].description;
				index++;
			}

			window.classificationMap = classificationMap;
			console.log('classificationMap:', classificationMap);
		}, 'json');
	},
		
// ----------------------------- improved for pagination function for namesearch result -- F.  Tumulak
	doNameSearch: function () {
		var params = {
			'mode': 'doNameFragSearch',
			'nameFrag': $('#nameFrag').val(),
			'timestamp': <?php echo $current_timestamp; ?>,
			'username': '<?php echo $_SESSION['username'] ?>'
		};

		$.ajax({
			url: mf.url,
			type: 'POST',
			dataType: 'json',
			headers: {
				'Authcheck': 'Token token="<?php echo hash_hmac('md5','doNameFragSearch-'.$_SESSION['username'].'-'.$current_timestamp,$_SESSION['secret_key']); ?>"'
			},
			data: params,
			error: function (xhr) {
				$('#errSpace').html('Please Re-login: ' + xhr.responseText).show();
			},
			success: function (results) {
				mf.allResults = results; // store results globally
				mf.currentPage = 1;
				mf.pageSize = 25;
				mf.renderPage();
				
				let searchTerm = $('#nameFrag').val().trim();
				$('#searchResultsTitle').text(`<?php echo T("SearchResults"); ?> ${searchTerm ? '"' + searchTerm + '"' : '<?php echo T("All Members"); ?>'}`);

			}
		});
	},

	// ----------------------------- pagination function, renderPage for namesearch result -- F.  Tumulak
	renderPage: function () {
		let results = mf.allResults || [];
		let page = mf.currentPage || 1;
		let pageSize = mf.pageSize || 25;

		let start = (page - 1) * pageSize;
		let end = Math.min(start + pageSize, results.length);

		let html = '';
		
		if (results.length === 0) {
			html = '<tr><td><?php echo T('no results') ?></td></tr>';
		} else {
			for (let i = start; i < end; i++) {
				let mbr = results[i];
				html += '<tr>\n';
				html += '  <td>' + mbr.barcode_nmbr + '</td>\n';
				if (mbr.hasOwnProperty('first_legal_name') || mbr.hasOwnProperty('last_legal_name')) {
					html += '  <td><i>' + mf.doConcatLegalName(mbr) + ', <?php echo T('see'); ?> </i><a href="#" id="' + mbr.mbrid + '">' + mbr.last_name + ', ' + mbr.first_name + '</a></td>\n';
				} else {
					html += '  <td><a href="#" id="' + mbr.mbrid + '">' + mbr.last_name + ', ' + mbr.first_name + '</a></td>\n';
				}
				html += '  <td>' + mbr.home_phone + '</td>\n';
				html += '<td>' + (classificationMap[mbr.classification] || 'Unknown') + '</td>\n';
				html += '</tr>\n';
			}
		}

		$('#srchRslts').html(html);
		$('#searchDiv').hide();
		$('#listDiv').show();

		$('#srchRslts tr:odd td').addClass('altBG');
		$('#srchRslts tr:even td').addClass('altBG2');

		$('.rsltQuan').html(`Showing ${start + 1} to ${end} of ${results.length}`);

		$('#srchRslts a').off('click').on('click', function (e) {
			e.preventDefault();
			mf.mbrid = e.target.id;
			mf.doFetchMember();
			$('#listDiv').hide();
		});

		// Disable prev/next buttons accordingly
		$('.goPrevBtn').prop('disabled', page <= 1);
		$('.goNextBtn').prop('disabled', end >= results.length);
	},
	// --------------------- for doNameSearch use ----------------

	handleMbrResponse: function (jsonInpt) {
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.mbr = JSON.parse(jsonInpt);
				if (mf.mbr == null) {
	  			mf.showMsg('<?php echo T("Nothing Found") ?>');
				}
				else {
					mf.multiMode = false;
					mf.getMbrSite();
				}
	    }
		  $('#searchDiv').hide();
	    $('#mbrDiv').show();
	},

	getMbrSite: function () {
		$.post(mf.url,{mode:'getSite', 'siteid':mf.mbr.siteid}, function (response) {
			mf.calCd = response['calendar'];
			mf.getMbrType();
		}, 'json');
	},

	// --------------------- for doBarCdSearch use ----------------
	
	handleMbrResponse_barcd: function (jsonInpt) {
		if ($.trim(jsonInpt).substr(0, 1) != '{') {
			$('#errSpace').html(jsonInpt).show();
			$('#mbrDiv').hide(); // Hide form if error
			return;
		}

		mf.mbr = JSON.parse(jsonInpt);
		console.log("Parsed mf.mbr:", mf.mbr);

		if (!mf.mbr || mf.mbr.mbrid === "0") {
			mf.showMsg('<?php echo T("Nothing Found") ?>');
			$('#mbrDiv').hide();
			return;
		}

		// Wait until we finish getting the site
		mf.multiMode = false;
		mf.getMbrSite_barcd(); // -> will continue in its callback
	},
	
	getMbrSite_barcd: function () {
		$.post(mf.url, { mode: 'getSite', siteid: mf.mbr.siteid }, function (response) {
			mf.calCd = response['calendar'];
			mf.getMbrType(); // this may also be async

			// ✅ Final validation AFTER all info fetched
 
				$('#mbrDiv').show();
				$('#searchDiv').hide();

		}, 'json');
	},
	

	// -------------------------------
	
	getMbrType: function () {
		$.post(mf.url,{mode:'getMbrType', 'classification':mf.mbr.classification}, function (response) {
			mf.typeInfo = response;
			mf.showOneMbr(mf.mbr)
		}, 'json');
	},

	//------------------------------
	//----------- add var definition for the new fields memberType and loan_Allotment --F.Tumulak
	showOneMbr: function (mbr) {
			var 
				mbrType = mf.typeInfo.description,
				loanAllotment = mf.typeInfo.loan_allotment;
				$('#newmemberType').val(mbrType);
				$('#loan_Allotment').val(loanAllotment);
		
		$('#mbrName').val(mbr.last_name+', '+mbr.first_name);
		$('#mbrSite').val("...");
		$('#mbrCardNo').val(mbr.barcode_nmbr);
		mf.mbrid = mbr.mbrid;
		mf.doGetCheckOuts(mf.mbrid);
		$.post(mf.url,{mode:'getSite', 'siteid':mbr.siteid}, function (response) {
			$('#mbrSite').val(response.name);
		}, 'json');
	},
	
	doGetCheckOuts: function () {
		$('#msgDiv').hide();
		$('#userMsg').html('');
		
		var ttlOwed = 0.00,
			mbrType = mf.typeInfo.description,
			loanAllotment = mf.typeInfo.loan_allotment,
			maxFines = mf.typeInfo.max_fines,
	  		params = 'mode=getChkOuts&mbrid='+mf.mbrid;
				//console.log('Member type: ', typeof(mbrType));
	    $.post(mf.url,params, function(jsonInpt){
			if (jsonInpt.substr(0,1) == '<') {
				$('#userMsg').html(jsonInpt);
				$('#msgDiv').show();
			} else if ($.trim(jsonInpt) == '[]') {
				mf.cpys = [];
				$('#chkOutList tBody').html('');
			} else if ($.trim(jsonInpt).substr(0,2) != '[{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.cpys = JSON.parse(jsonInpt);
				var html = '';
				
				// --------------- adjusted daysLate compute with Calendar logic applied -- F. Tumulak 
				// --------------- adjusted daysLate compute with Calendar logic applied -- F. Tumulak 
				// --------------- adjusted daysLate compute with Calendar logic applied -- F. Tumulak 
				
				for (var nCpy in mf.cpys) {
					var cpy = mf.cpys[nCpy],
						outDate = new Date(cpy.out_dt),
						dueDate = new Date(cpy.due_dt);

					// Adjust dueDate if it falls on weekend, this still needs some improving but one step at a time --F.Tumulak 
					// let day = dueDate.getDay(); // 0 = Sunday, 6 = Saturday
					// if (day === 6) {
						// dueDate.setDate(dueDate.getDate() + 2); // Saturday → Monday
					// } else if (day === 0) {
						// dueDate.setDate(dueDate.getDate() + 1); // Sunday → Monday
					// }

					// Add loan allotment if needed (assume it's a number)
					let loanPeriod = Math.round((dueDate - outDate) / (1000 * 60 * 60 * 24));

					let daysLate = Math.max(0, cpy.daysLate - loanPeriod),
						lateFee = ((cpy.lateFee === null) ? '0' : (cpy.lateFee).toLocaleString()),
						owed = (daysLate * cpy.lateFee).toFixed(2);

					// Format dueDate as 'Fri Jan 17 2025'
					let dueDateFormatted = dueDate.toLocaleDateString('en-US', {
						weekday: 'short', year: 'numeric', month: 'short', day: '2-digit'
					});
					
					let dateOutFormatted = outDate.toLocaleDateString('en-US', {
						weekday: 'short', year: 'numeric', month: 'short', day: '2-digit'
					});

					html += '<tr>';
					html += '	<td style="text-align: center;" >' + dateOutFormatted + '</td>';
					html += '	<td style="text-align: center;" >' + cpy.media + '	</td>\n';
					html += '	<td style="text-align: center;" >' + cpy.barcode + '</td>';
					html += '	<td style="text-align: center;" ><a href="#" id="' + cpy.bibid + '">' + shortenTitle(cpy.title, 75) + '</a></td>';
					html += '	<td style="text-align: center;" >' + dueDateFormatted + '</td>';
					html += '	<td style="text-align: center;" class="number"><b>' + daysLate + '@' + lateFee + '</b></td>';
					html += '	<td style="text-align: center;" class="number">' + owed + '</td>';
					html += '</tr>\n';

					ttlOwed += parseFloat(owed);
				}
				
				// ---------------  ---------------  ---------------  ---------------  ---------------

				mf.nmbrOnloan = nCpy+1;
				$('#chkOutList tBody').html(html);
				$('table tbody.striped tr:odd td').addClass('altBG');
				$('table tbody.striped tr:even td').addClass('altBG2');	

				if (ttlOwed >= maxFines && ttlOwed != 0) {
					$('#chkOutBtn').disable();
					$('#ckoutBarcd').disable();
					$('#chkOutMsg').html('<?php echo T("NotAllowed");?>').show();
				}
				$('#maxFine').html((Number(maxFines).toFixed(2)).toLocaleString());
				$('#ttlOwed').html((Number(ttlOwed).toFixed(2)).toLocaleString());
				$('#newmemberType').val(mbrType);
				$('#loan_Allotment').val(loanAllotment);

				$('#chkOutList a').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					idis.init(mf.opts, mf.sites); // be sure all is ready
					idis.doBibidSearch(e.target.id);
					$('#biblioDiv').show();
					$('#mbrDiv').hide();					
				});			
	    }
		});
		mf.doGetHolds();
	},
	
	doGetHolds: function () {
    $('#holdList tBody').html('');
	  var params = 'mode=getHolds&mbrid='+mf.mbrid;
	  $.post(mf.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) == '<') {
				mf.showMsg(jsonInpt);
			} else {
				mf.holds = JSON.parse(jsonInpt);
				if (! mf.holds) {
	  			mf.showMsg('<?php echo T("Nothing Found") ?>');
				}
				else {
					var html = '';
					for (var nHold in mf.holds) {
						var hold = mf.holds[nHold];
						var holdDate = hold.hold_dt.split(' ')[0];
						if (hold.due_dt) 
							var dueDate = hold.due_dt.split(' ')[0];
						else
							var dueDate = 'n/a';
						html += '<tr>'
						html += '	<td> \n';
						html += '		<input type="button" class="holdDelBtn" value="<?php echo T("del");?>" /> \n';
						html += '		<input type="hidden" value="'+hold.holdid+'" /></td> \n';
						html += '	</td> \n';
						html += '	<td>'+holdDate+'</td>';
						html += '	<td>'+hold.barcode+'</td>';
						html += '	<td><a href="#" id="'+hold.bibid+'">"'+hold.title+'"</a></td>';
						html += '	<td>'+hold.status+'</td>';
						html += '	<td>'+dueDate+'</td>';
						html += '</tr>\n';

					}
					$('#holdList tBody').html(html);
					$('table tbody.striped tr:odd td').addClass('altBG');
					$('table tbody.striped tr:even td').addClass('altBG2');
					$('.holdDelBtn').on('click',null,mf.doDelHold);
					$('#holdList a').on('click',null,function (e) {
						e.preventDefault(); e.stopPropagation();
						idis.init(mf.opts); // be sure all is ready	
						idis.doBibidSearch(e.target.id);
						$('#biblioDiv').show();
						$('#mbrDiv').hide();					
					});			
				}
	    }
		});
	},
	
	//------------------------------
	doShowMbrAcnt: function () {
	  var params = 'mode=getAcntActivity&mbrid='+mf.mbrid;
	  $.post(mf.url,params, function(jsonInpt){
			$('#tranList tBody').html(''); // clear any residue from past displays
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#userMsg').html(jsonInpt);
				$('#msgDiv').show();
			} else {
				mf.trans = JSON.parse(jsonInpt);
				var html = '';
				if (!mf.trans) {
					html += '<tr>'
					html += '<td colspan="6"><?php echo T("No transactions found."); ?></td> \n';
					html += '</tr>\n';
				} else {
					var bal = parseFloat(0.0);
					html += '<tr> \n';
					html += '	<td colspan="3">&nbsp</td> \n';
					html += '	<td colspan="2" class="smallType center"><?php echo T("Opening Balance"); ?></td> \n';
					html += '	<td class="number">'+bal.toFixed(2)+'</td> \n';
					html += '</tr> \n';
					for (var nTran in mf.trans) {
						var tran = mf.trans[nTran];
						bal += parseFloat(tran.amount);
						html += '<tr> \n';
						html += '	<td> \n';
						html += '		<input type="button" class="acntTranDelBtn" value="<?php echo T("del");?>" /> \n';
						html += '		<input type="hidden" value="'+tran.transid+'" /></td> \n';
						html += '	</td> \n';
						html += '	<td class="date">'+tran.create_dt.split(' ')[0]+'</td> \n';
						html += '	<td>'+mf.tranType[tran.transaction_type_cd]+'</td> \n';
						html += '	<td>'+tran.description+'</td> \n';
						html += '	<td class="number">'+(parseFloat(tran.amount)).toFixed(2)+'</td> \n';
						html += '	<td class="number">'+bal.toFixed(2)+'</td> \n';
						html += '</tr> \n';
					}
					$('#tranList tBody').html(html);
					$('#tranList tbody.striped tr:odd td').addClass('altBG');
					$('#tranList tbody.striped tr:even td').addClass('altBG2');
					$('.acntTranDelBtn').on('click',null,mf.doDelAcntTrans);
				};			
			}
		});
		$('#mbrDiv').hide();
		$('#acntDiv').show();
		
	},
	doTransAdd: function (e) {
		e.preventDefault;
		e.stopPropagation;
		$('#acntMbrid').val(mf.mbrid);
		var parms = $('#acntForm').serialize();
		//console.log('adding: '+parms);
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				document.forms.acntForm.reset();
				mf.showMsg('Added!');
				mf.doShowMbrAcnt();
			}
		});
		return false;
	},
	doDelAcntTrans: function (e) {
		var transid = $(this).next().val();
		if (!confirm('<?php echo T("Are you sure you want to delete "); ?>'+'this transaction?')) return false;

  	var parms = {	'mode':'d-3-L-3-tAcntTrans', 'mbrid':mf.mbrid, 'transid':transid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				mf.showMsg('transaction deleted!');
		  	//mf.rtnToMbr();
				mf.doShowMbrAcnt();
			}
		});
	},
	
	//------------------------------
	doCheckout: function () {
		$('#userMsg').html('');
		$('#msgDiv').hide();

		var barcd = $.trim($('#ckoutBarcd').val());
		if (barcd == '') {
			mf.showMsg('Please enter a number');
			return false;
		}
		barcd = flos.pad(barcd, mf.opts.item_barcode_width, '0');
		$('#ckoutBarcd').val(barcd); // redisplay expanded value

		// ✅ GET the loan allotment value
		var loanAllotment = parseInt($('#loan_Allotment').val()) || 0;

		var parms = {
			'mode': 'doCheckout',
			'mbrid': mf.mbr.mbrid,
			'barcodeNmbr': barcd,
			'calCd': mf.calCd,
			'loan_Allotment': loanAllotment // ✅ ADD it to the request
		};

		$.post(mf.url, parms, function(response) {
			if (response == '') {
				mf.showMsg('Checkout Completed!');
				$('#ckoutBarcd').val('')
				mf.showOneMbr(mf.mbr);  // refresh member with latest checkout list
			} else {
				mf.showMsg(response);
			}
		});

		return false;
	},


	//------------------------------
	doHold: function () {
		var barcd = $.trim($('#holdBarcd').val());
		// barcd = flos.pad(barcd,mf.opts.item_barcode_width,'0');

		// inform user about the blank entry. --F.Tumulak
		if (barcd == '') {
			mf.showMsg('Please enter a number');
			return false;
		}
		// pad the barcode values with zeroes to match it with the encoded ones --F.Tumulak
		// this would work in nicely similar to the doCheckout function. --F .Tumulak

		barcd = flos.pad(barcd, mf.opts.item_barcode_width, '0');
		$('#holdBarcd').val(barcd); // redisplay expanded value


		var parms = {'mode':'doHold', 'mbrid':mf.mbrid, 'barcodeNmbr':barcd};
		$.post(mf.url, parms, function(response) {
			if (response == '<') {
				mf.showMsg(response);
			} else {
				mf.showMsg('Hold Completed!');
				$('#holdBarcd').val('')
				mf.showOneMbr(mf.mbr)
			}
		});
		return false;
	},
	doDelHold: function (event) {
		var $delBtn = $(event.target);
		$delBtn.parent().parent().addClass('hilite');
		if (!confirm('<?php echo T("Are you sure you want to delete this");?>'+' hold?')) return false;
		
		var holdid = $delBtn.next().val();
  	var parms = {	'mode':'d-3-L-3-tHold', 'mbrid':mf.mbrid, 'holdid':holdid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				mf.showMsg('hold deleted!');
				mf.showOneMbr(mf.mbr)
			}
		});
	},
	
	//------------------------------
	doShowMbrHelp: function () {
		$('#mbrDiv').hide();
		$('#helpDiv').show();  // This line handles both show and hide
	},

	//------------------------------	
	
	doShowMbrHist: function () {
		$('#mbrDiv').hide();
		$('#histDiv').show();
		var statMap = {'crt':'IN', 'in':'IN', 'out':'OUT'};
	  $.post(mf.url,{mode:'getHist', 'mbrid':mf.mbrid}, function(jsonInpt){
			$('#histList tBody').html(''); // clear any residue from past displays
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#userMsg').html(jsonInpt);
				$('#msgDiv').show();
			} else {
				mf.hist = JSON.parse(jsonInpt);
				var html = '';
				if (!mf.hist) {
					html += '<tr>'
					html += '<td colspan="6"><?php echo T("No transactions found."); ?></td> \n';
					html += '</tr>\n';
				} else {
					for (var nHist in mf.hist) {
						var hist = mf.hist[nHist];
						html += '<tr> \n';
						html += '	<td>'+hist.title+'</td> \n';
						html += '	<td>'+statMap[hist.status_cd]+'</td> \n';
						html += '	<td class="date">'+hist.status_begin_dt.split(' ')[0]+'</td> \n';
						html += '</tr> \n';
					}
					$('#histList tBody').html(html);
					$('#histList tbody.striped tr:odd td').addClass('altBG');
					$('#histList tbody.striped tr:even td').addClass('altBG2');
				};
			}
		});
		return false;
	},
	
	//------------------------------
	doShowMbrDetails: function (e) {
		var mbr = mf.mbr;
		$('#addMbrBtn').hide();
		$('#updtMbrBtn').show().enable();
		$('#deltMbrBtn').show().enable();
		$('.gobkUpdtBtn').show();
		$('#editHdr').html('<?php echo T("Edit Member Info"); ?>');
		$('#editMode').val('updateMember');

		$('#mbrid').val(mbr.mbrid);
		$('#siteid').val(mbr.siteid);

		// folowing 'readonly' if existing member
		$('#barcode_nmbr').val(mbr.barcode_nmbr);
		if (mbr.barcode_nmbr) {
			$('#barcode_nmbr').attr('readonly','readonly');
		} else {
			$('#barcode_nmbr').removeAttr('readonly');
		}

		$('#last_name').val(mbr.last_name);
		$('#first_name').val(mbr.first_name);
		$('#address1').val(mbr.address1);
		$('#address2').val(mbr.address2);
		$('#city').val(mbr.city);
		$('#state').val(mbr.state);
		$('#zip').val(mbr.zip);
		$('#zip_ext').val(mbr.zip_ext);
		$('#home_phone').val(mbr.home_phone);
		$('#work_phone').val(mbr.work_phone);
		$('#email').val(mbr.email);
		$('#classification').val(mbr.classification);

		$.each(mf.cstmFlds, function (n, value) {
			var fld = value,
					code=fld.code;
			$('#custom_'+code).val(mbr[code]);
		});

		$('#mbrDiv').hide();
		$('#editDiv').show();
	},
	doShowMbrAdd: function () {
		$('#addMbrBtn').show();
		$('#updtMbrBtn').hide();
		$('#deltMbrBtn').hide();
		$('#msgDiv').hide();
		$('.gobkNewBtn').show();
		document.forms.editForm.reset();

		$('#mbrid').val('');
		$('#siteid').val([$('#crntSite').val()]);
		$('#city').val([$('#crntCity').val()]);
		mf.getNewBarCd();  // posts directly to screen

		$('#searchDiv').hide();
		$('#editHdr').html('<?php T("Add New Member"); ?>');
		$('#editMode').val('addNewMember');
		//mf.showMsg('Added!');
		$('#editDiv').show();
	},
	
	doMbrAdd: function () {
		$('#msgDiv').hide();

		// Add a basic client-side validation --F.Tumulak 
		let requiredFields = ['#last_name', '#first_name', '#home_phone'];
		for (let i = 0; i < requiredFields.length; i++) {
			let field = $(requiredFields[i]);
			if ($.trim(field.val()) === '') {
				let fieldName = requiredFields[i].replace('#', '').replace('_', ' ');
				mf.showMsg('Please fill in the <b>' + fieldName + '</b> field.');
				
				field.focus();
				return false;
			}
		}

		var parms = $('#editForm').serialize();
		$.post(mf.url, parms, function(response) {
			if (response == 0) {
				mf.rtnToSrch();
				mf.showMsg('<?php echo T("Added");?>');
				setTimeout(function () {
					$('#msgDiv').show().hide(2000);
				}, 3000);
			} else {
				mf.showMsg(response);
			}
		});
		return false;
	},
	doMbrUpdate: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();

		// Basic client-side validation
		let requiredFields = ['#last_name', '#first_name', '#home_phone'];
		for (let i = 0; i < requiredFields.length; i++) {
			let field = $(requiredFields[i]);
			if ($.trim(field.val()) === '') {
				let fieldName = requiredFields[i].replace('#', '').replace('_', ' ');
				mf.showMsg('Please fill in the ' + fieldName + ' field.');
				field.focus();
				return false;
			}
		}

		var parms = $('#editForm').serialize();
		$.post(mf.url, parms, function(response) {
			if (response.substr(0, 1) == '<') {
				// likely an HTML error response
				mf.showMsg(response);
			} else {
				if (response.substr(0, 1) == '1') {
					$('#updateMsg').html('<?php echo T("Updated");?>');
					$('#updateMsg').show();
				}
				mf.showMsg('Updated!');
				mf.doFetchMember();
				$('#editDiv').hide();
			}
		});

		$('#updtMbrBtn').disable();
		$('#deltMbrBtn').disable();
		return false;
	},
	
	doDeleteMember: function () {
		if (mf.nmbrOnloan > 0) {
			alert('<?php echo T("You must settle all outstanding loans before deleting a member."); ?>');
			return false;
		}
		var delConfirmMsg = '<?php echo T("Are you sure you want to delete "); ?>';
		if (!confirm(delConfirmMsg + ' ' + mf.mbr.first_name + ' ' + mf.mbr.last_name+'?')) return false;

  	var parms = {	'mode':'d-3-L-3-tMember', 'mbrid':mf.mbrid };
  	$.post(mf.url, parms, function(response){
			console.log('response is: ' + response);
			if (!response) { // if zero, successfully deleted --> F.Tumulak
				// console.log('rcvd error msg from server :<br />'+response);
				// response is one, failed to delete
				mf.showMsg(response);
			}
			else { //response is zero, so it is successful!
								// response is zero, success ->corrected by Ferdinand Tumulak
                mf.rtnToSrch();
				mf.showMsg('Member deleted: ' + mf.mbrid);
                setTimeout( function(){
                    $('#msgDiv').show().hide(2000);
                  }  , 3000 );
			}
		});
	},
	doConcatLegalName: function (mbr) {
		if (mbr['first_legal_name']) {
			if (mbr['last_legal_name']) {
				return mbr.first_legal_name+' '+mbr.last_legal_name;
			} else {
				return mbr.first_legal_name+' '+mbr.last_name;
			}
		} else if (mbr['last_legal_name']) {
			return mbr.first_name+' '+mbr.last_legal_name;
		} else {
			return mbr.first_name+' '+mbr.last_name;
		}
	},
};
$(document).ready(mf.init);

</script>

