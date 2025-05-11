<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";
var img = {
	init: function () {
		img.url = '../opac/imageSrvr.php';
		img.initWidgets();

		$('#orderBy').on('change',null,function () {
			img.fetchFotoPage(0);
		});
		$('.nextBtn').on('click',null,img.getNextPage);
		$('.prevBtn').on('click',null,img.getPrevPage);
		$('.gobkBiblioBtn').on('click',null,img.rtnToGallery);

		img.resetForm();
    img.fetchFotoPage();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		img.firstItem = 0;
		img.srchType = '';
		$('#workDiv').hide();
		$('#biblioDiv').hide();
		$('#fotoDiv').show();
		$('#prevBtn').disable();
		$('#nextBtn').disable();
	},
	rtnToGallery: function () {
		$('#biblioDiv').hide();
		$('#fotoDiv').show();
	},

	//------------------------------
	fetchOpts: function () {
	    /*
        $.post(img.url,{mode:'getOpts'}, function(jsonData){
	        img.opts = jsonData
		}, 'json');
        */
        img.opts = list.getOpts();
	},
	//------------------------------
	getNextPage:function () {
		$('.nextBtn').disable();
		img.fetchFotoPage(img.nextPageItem);
	},

	getPrevPage:function () {
		$('.prevBtn').disable();
		img.fetchFotoPage(img.prevPageItem);
	},

	fetchFotoPage: function (firstItem) {
		if (firstItem === undefined) firstItem = img.firstItem;
		img.orderBy = $('#orderBy option:selected').val();
	    $.post(img.url,{'mode':				'getPage',
											 'orderBy':			img.orderBy,
											 'firstItem':		firstItem,
											 'tab':					$('#tab').val(),
											}, function(data){
			img.firstItem = parseInt(data.firstItem);
			img.lastItem = parseInt(data.lastItem);
			img.perPage = parseInt(data.perPage);
			img.columns = parseInt(data.columns);
			img.ttlNmbr = parseInt(data.nmbr);
			img.cells = data.tbl;

			img.showFotos();
			$(window).on('resize',null,img.showFotos);
		}, 'json');
	},
	showFotos: function () {
			$('.countBox').html((img.firstItem+1)+' - '+img.lastItem+' <?php echo T("of");?> '+img.ttlNmbr).show();

			var $fotos = $('#fotos'),
				tab = '<?php echo $tab;?>',
				html = '',
				cntr = 0;

			$fotos.html('');
			for (var entry in img.cells) {
				var cell = img.cells[entry];

				var bibid = cell['bibid'];

				html = '<li>'+"\n";
				html += '	<div class="galleryBox">'+"\n";
				html += '		<div><img id="img-'+bibid+'" src="../photos/'+cell['url']+'" class="biblioImage" /></div>'+"\n";
				html += '		<div class="smallType">'+"\n";
				html += '			<a href="#" id="a-'+bibid+'" >'+cell[img.orderBy]+'</a>'+"\n";
				html += '		</div>'+"\n";
				html += '</li>'+"\n";
				$fotos.append(html);

				$('#fotos img').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					var id = e.currentTarget.id;
					$('#'+id).toggleClass('resize');
				});
				$('#fotos a').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					idis.init(img.opts); // be sure all is ready
					var idParts = e.currentTarget.id.split('-');
					idis.doBibidSearch(idParts[1]);
					$('#biblioDiv').show();
					$('#fotoDiv').hide();
				});

			}
			$('#gallery').show();

			// enable or disable next / prev buttons
			if(img.firstItem >= img.perPage){
				img.prevPageItem = img.firstItem - img.perPage;
				$('.prevBtn').enable();
			} else {
				$('.prevBtn').disable();
			}
			if(((img.perPage+img.firstItem) <= img.lastItem) && (img.ttlNmbr > img.lastItem)){
				img.nextPageItem = img.perPage + img.firstItem;
				$('.nextBtn').enable();
			} else {
				$('.nextBtn').disable();
			}
	},

}
$(document).ready(img.init);

</script>
