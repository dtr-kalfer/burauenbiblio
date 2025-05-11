<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.

  	this code should be explicity initialized when needed unless
	the frequent appearance of the video allow/deny prompt is acceptable

    in Mozilla Firefox 46+, you can eleminate the prompt to allow video capture
    at "about:config | media.navigator.permission.disabled"
    BUT BE WARY, once on, the camera is accesable by any page in the browser until OB is turned off
 */
?>
// JavaScript Document
"use strict";

var wc = {
	init: function () {
console.log('wc.init() called');
		wc.url = '../catalog/catalogServer.php';
		$('.help').hide();

		wc.showFotos = false;
        <?php if ($_SESSION['show_item_photos'] == 'Y') { ?>
		wc.showFotos = true;
        <?php } ?>
		if (!wc.showFotos) return;

		wc.fotoRotate = <?php echo Settings::get('thumbnail_rotation');?> || 0;
		wc.fotoWidth = <?php echo Settings::get('thumbnail_width');?> || 0;
		wc.fotoHeight = <?php echo Settings::get('thumbnail_height');?> || 0;

		$('#video').attr('width',wc.fotoWidth).attr('height',wc.fotoHeight);

		/* folowing dimensions are not an error, the box MUST be square for later image rotation */
		wc.canvasIn = document.getElementById('canvasIn'),
		$('#canvasIn').attr('width',wc.fotoWidth).attr('height',wc.fotoHeight);
		wc.ctxIn = wc.canvasIn.getContext('2d');

		/* folowing dimensions are not an error, the box MUST be turned to accept the rotation */
		wc.canvasOut = document.getElementById('canvasOut'),
		$('#canvasOut').attr('width',wc.fotoWidth).attr('height',wc.fotoHeight);
		wc.ctxOut = wc.canvasOut.getContext('2d');

		wc.eraseImage();

		wc.initWidgets();

		$('input.fotoSrceBtns').on('click',null,wc.changeImgSource);
		$('#capture').on('click',null,wc.takeFoto);
		$('#browse').on('change',null,wc.getFotoFile);
		$('#addFotoBtn').on('click',null,wc.sendFoto);
		$('#updtFotoBtn').on('click',null,wc.doUpdatePhoto);
		$('#deltFotoBtn').on('click',null,wc.doDeletePhoto);

		/* support drag and drop of image */
		wc.canvasOut.ondragover = function (e){
			// keep browser from replacing entire page with dropped image //
            e.preventDefault();
			return false;
		};
		wc.canvasOut.ondrop = function (e) {
			wc.getFotoDrop(e);
		};

		/* support cut&paste of image */
		document.onpaste = function (e) {
			wc.getFotoPaste(e);
		};

		wc.resetForm();

	},
	//------------------------------
	initWidgets: function () {
console.log('in wc::initWidgets()');

		/* Video Initialization; needed forcamera input */
		wc.fotoRotate = <?php echo Settings::get('thumbnail_rotation');?> || 0;
		wc.cameraId = "<?php echo Settings::get('camera');?>";

	    wc.video = document.querySelector('video');
console.log(wc.cameraId);
		const constraints =  {
			video:{width: { min: wc.fotoWidth },
				   height: { min: wc.fotoHeight },
//				   deviceId: { exact: wc.cameraId},
				  },
			audio: false,
			};
console.log("in wc::initWidgets(), constraints set.");

	    navigator.mediaDevices.getUserMedia(constraints)// returns a Promise, obviously
        .then(stream => {
  			wc.video.srcObject = stream;
  			wc.video.onloadedmetadata = function(e) {
    			wc.video.play();
  			};

			$('#canvasIn').attr('width',wc.video.width).attr('height',wc.video.height);
			wc.ctxIn.clearRect(0,0, canvasIn.width, canvasIn.height);
			wc.ctxIn.drawImage(wc.video, 0,0);
		})
        .catch(function(err) { console.log(err.name + ": " + err.message); });
	},

    vidOff: function () {
        wc.video.pause();
		if (wc.video.srcObject) {
        	wc.video.srcObject.getTracks()[0].stop();
			wc.video.srcObject=null;
		}
        console.log("Vid off");
    },

	//----//
	resetForm: function () {
		$('.help').hide();
        //$('#fotoDiv').hide();
		//$('#video').hide();
		//$('#canvasIn').hide();
		$('#fotoAreaDiv').show();
		$('#addFotoBtn').show();
		wc.eraseImage();
		wc.changeImgSource();
	},

	//----//
	changeImgSource: function (e) {
		var chkd = $('input[name=imgSrce]:checked', '#fotoForm').val();
		if (chkd == 'cam') {
			$('#camera').attr('autoplay',true);
			//$('#fotoName').val('filename.jpg');
			$('#capture').show();
			$('#browse').hide();
		} else if (chkd == 'brw') {
			$('#camera').removeAttr('autoplay');
			//$('#fotoName').val('');
			$('#capture').hide();
			$('#browse').show();
		}
	},

	//----//
/*
	rotateImage: function (degrees) {
        var tw = wc.canvasIn.width/2,
    		th = wc.canvasIn.height/2,
    		angle = degrees*(Math.PI/180.0);

		wc.ctxIn.save();
		wc.ctxIn.translate(tw, th);
		wc.ctxIn.rotate(angle);
		wc.ctxIn.translate(-th, -tw);
		wc.ctxIn.drawImage(canvasIn, 0,0);
		wc.ctxIn.restore();
	},
*/
	showImage: function (fn) {
		var img = new Image;
		img.onload = function() { wc.ctxOut.drawImage(img, 0,0, wc.fotoWidth,wc.fotoHeight); };
		img.src = fn;
	},
	eraseImage: function () {
		wc.ctxOut.clearRect(0,0, wc.canvasOut.width,wc.canvasOut.height)
		wc.ctxIn.clearRect(0,0, wc.canvasIn.width,wc.canvasIn.height)
	},

	//------------------------------
	getFotoPaste: function (e) {
		e.preventDefault();
		if (e.clipboardData &&e.clipboardData.items) {
			var items = e.clipboardData.items;
			for (var i=0; i<items.length; i++) {
				if (items[i].kind === 'file' && items[i].type.match(/^image/)) {
					wc.readFile(items[i].getAsFile());
					break;
				}
			}
		}
		return false;
	},
	getFotoDrop: function (e) {
		e.preventDefault();
		e = e || window.event;
		var files = e.dataTransfer.files;
		if (files) wc.readFile(files[0]);
	},
	getFotoFile: function (e) {
		// Get the FileList object from the file select event
		var files = e.target.files;
		if(files.length === 0) return;
		var file = files[0];
		if(file.type !== '' && !file.type.match('image.*')) return;
		//$('#fotoName').val($('#browse').val());
		wc.readFile(file)
	},
	readFile: function (file) {
		var reader = new FileReader();  // output is to reader.result
		reader.onerror = function () {
			console.log('FileReader error: '+reader.error);
			return;
		}
		reader.onloadend = function(e) {
        	var tempImg = new Image();
        	tempImg.src = reader.result;
        	tempImg.onload = function() {
                wc.ctxOut.drawImage(tempImg, 0, 0, wc.fotoWidth,wc.fotoHeight);
    		}
		};
        reader.readAsDataURL(file);
	},
	takeFoto: function () {
		var x, y;
		//console.log('in photoEditorJs, rotate angle= '+wc.fotoRotate+'deg');
	    if(wc.fotoRotate == 0 || wc.fotoRotate == 180) {
			wc.canvasOut.width = wc.video.width;
			wc.canvasOut.height = wc.video.height;
			y = wc.video.height/2;
			x = wc.video.width/2
	    } else {
			wc.canvasOut.width = wc.video.height;
			wc.canvasOut.height = wc.video.width;
			y = wc.video.height/2;
			x = wc.video.width/2
	    }

		// save current coord system
		wc.ctxOut.save();
		// move coord system 0,0 point to center of expected image
	    wc.ctxOut.translate(x, y);
		// rotate the coord system about the 0,0 point
	    wc.ctxOut.rotate(wc.fotoRotate*Math.PI/180);
		// draw image so it's center lays on 0,0 pt
	    wc.ctxOut.drawImage(wc.video, -x,-y, wc.canvasOut.height,wc.canvasOut.width);
//	    wc.ctxOut.drawImage(wc.video, 0,0, wc.video.width, wc.video.height, -x,-y, wc.canvasOut.height,wc.canvasOut.width);
		// recover original coord system
		wc.ctxOut.restore();
	},
	sendFoto: function (e) {
		//console.log('sending foto');
		if (e) e.stopPropagation();
//		obib.hideMsg(); // clear any user msgs
		var current_add_mode = '',
			url = $('#fotoName').val(),
			bibid = $('#fotoBibid').val();
		var imgMode = (url.substr(-3) == 'png')? 'image/png' : 'image/jpeg';

		current_add_mode = $('.fotoSrceBtns:checked').val();
		if ((current_add_mode == 'brw') || (current_add_mode == 'cam')) {
			$.post(wc.url,
				{'mode':'addNewPhoto',
			 	 'type':'base64',
			 	 'img':wc.canvasOut.toDataURL(imgMode, 0.8),
                 'bibid':bibid,
			 	 'url': url,
                 'position':0,
				},
				function (data) {
					//console.log('local image posting OK');
					var crntFotoUrl = data[0]['url'];
					// paste this photo to parent biblio (used by 'Existing Item' viewer)
					$('#bibBlkB').html('<img src="'+crntFotoUrl+'" id="biblioFoto" class="hover" '
						+ 'height="'+wc.fotoHeight+'" width="'+wc.fotoWidth+'" >');
					if (typeof bs !== 'undefined') {
						bs.crntFotoUrl = crntFotoUrl;
						bs.getPhoto(bibid, '#photo_'+bibid );
					}
					obib.showMsg('<?php echo T("cover photo posted");?>: '+crntFotoUrl);
					$('#photoAddBtn').hide();
					$('#photoEditBtn').show();
				}, 'json'
			);
		//e.preventDefault();
		} else {
			$.post(wc.url,
				{'mode':'addNewRemotePhoto',
               			'bibid':bibid,
			 	'url': url,
				},
				function (data) {
					//console.log('remote image posting OK');
					var crntFotoUrl = data[0]['url'];
					$('#bibBlkB').html('<img src="'+crntFotoUrl+'" id="biblioFoto" class="hover" '
							+ 'height="'+wc.fotoHeight+'" width="'+wc.fotoWidth+'" >');
					if (typeof bs !== 'undefined') {
						bs.crntFotoUrl = crntFotoUrl;
						bs.getPhoto(bibid, '#photo_'+bibid );
					}
					obib.showMsg('<?php echo T("cover photo posted");?>');
					$('#photoAddBtn').hide();
					$('#photoEditBtn').show();
				}, 'json'
			);
		}
		obib.showMsg('<?php echo T("cover photo posted");?>');
//		return false;
	},

	doUpdatePhoto: function (e) {
        var callFinishUpdate = true;
        wc.deleteActual(e , function(e) {wc.callFinishUpdte(e)}); // returns before actual work done by server
	},
    finishUpdate: function (e) {
		//console.log('attempting update');
        wc.sendFoto(e);
    },

	doDeletePhoto: function (e) {
		if (confirm("<?php echo T("Are you sure you want to delete this cover photo"); ?>")) {wc.deleteActual(e); }
    },
    deleteActual: function (e, forUpdate=false) {
  	    $.post(wc.url,{'mode':'deletePhoto',
				       'bibid':$('#fotoBibid').val(),
					   'url':$('#fotoName').val(),
	              },
				  function(response){
						//console.log('back from deleting');
						wc.eraseImage();
						$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" '
  											+ 'height="'+wc.fotoHeight+'" width="'+wc.fotoWidth+'" >');
                        idis.crntFoto = null;
						$('#photoAddBtn').show();
						$('#photoEditBtn').hide();

                        if(forUpdate) {
                            wc.finishUpdate();
                        } else {
					       $('#fotoName').val('');
						   obib.showMsg('<?php echo T("cover photo deleted");?>');
                       }
				 }
                 , 'json'
		);
		e.stopPropagation();
		return false;
	},

}

</script>
