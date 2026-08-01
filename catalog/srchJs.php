<?php
/**
 * JavaScript portion of the Biblio ExistingItem Manager
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Refactored for PHP 8.3 compatibility, safer JS, and better maintainability. -F. Tumulak
 *
 * Original openbiblio @author Luuk Jansen
 * Original openbiblio @author Fred LaPlante
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 1. PHP-side helpers: prepare values injected into JS
// ---------------------------------------------------------------------------
$opacMode       = in_array($tab, ['opac', 'circulation'], true);
$opacModeJs     = $opacMode ? 'true' : 'false';
$showMarcJs     = JS(T('Show Marc Tags'));
$hideMarcJs     = JS(T('Hide Marc Tags'));
$libName        = JS((string) ($_SESSION['libName'] ?? ''));
$whereAmI       = JS(T('curently viewing site') . ': ' . ($_SESSION['libName'] ?? ''));

// Translated strings for inline JS use
$tAll           = JS(T('All'));
$tSearching     = JS(T('Searching'));
$tNothingFound  = JS(T('Nothing Found'));
$tNothingByBarc = JS(T('NothingFoundByBarcdSearch'));
$tItems         = JS(T('Items'));
$tUpdate        = JS(T('Update'));
$tUpdateSuccess = JS(T('Update Biblio Success!'));
$tEditFoto      = JS(T('EditingExistingFoto'));
$tAddFoto       = JS(T('AddingNewFoto'));
$tEnterFoto     = JS(T('EnterNewPhotoInfo'));
$tCoverFoto     = JS(T('CoverPhotoFor'));

$showPhotos     = Settings::get('show_item_photos');
$barcdWidth     = (int) Settings::get('item_barcode_width');
$curSite        = JS(Settings::get('library_name'));
$uploadDir      = JS(OBIB_UPLOAD_DIR);
?>
<script defer>
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * JavaScript portion of the Biblio ExistingItem Manager
 * @author Luuk Jansen
 * @author Fred LaPlante
 */
"use strict";

// ---------------------------------------------------------------------------
// Mode flag set by PHP
// ---------------------------------------------------------------------------
var opacMode = <?php echo $opacModeJs; ?>;

// ---------------------------------------------------------------------------
// Main search object (namespace)
// ---------------------------------------------------------------------------
var bs = {
  showMarc:   "<?php echo $showMarcJs; ?>",
  hideMarc:   "<?php echo $hideMarcJs; ?>",
  whereAmI:   "<?php echo $whereAmI; ?>",
  multiMode:   false,

  // =========================================================================
  // init — kick everything off
  // =========================================================================
  init: function () {
    bs.initWidgets();

    // Server endpoints
    bs.url       = '../catalog/catalogServer.php';
    bs.listSrvr  = '../shared/listSrvr.php';
    bs.urlLookup = '../catalog/onlineServer.php'; // may not exist
    bs.opts      = {};
    bs.biblio    = [];

    $('#whereAmI').html(bs.whereAmI);

    // --- Search form bindings ---
    $('#barcdSrchBtn').on('click', null, bs.doBarcdSearch);
    $('#phraseSrchBtn').on('click', null, bs.doPhraseSearch);
    bs.srchBtnClr = $('#phraseSrchBtn').css('color');

    $('#bc_searchBarcd').on('keyup',  null, bs.checkBarcdSrchBtn);
    $('#bc_searchBarcd').on('change', null, bs.formatBarcode);
    $('#bc_searchBarcd').on('input change', bs.checkBarcdSrchBtn);

    $('#ph_searchText').on('keyup',  null, bs.checkPhraseSrchBtn);
    $('#ph_searchText').on('input change', bs.checkPhraseSrchBtn);

    // --- Advanced search toggle ---
    $('#advancedSrch').hide();
    $('#advanceQ').prop('checked', false);
    $('#advanceQ').on('click', null, function () {
      $('#advancedSrch').toggle($('#advanceQ').prop('checked'));
    });

    // --- Results / biblio section bindings ---
    $('#addNewBtn').on('click', null, bs.doNewCopy);
    $('#addItem2CartBtn').on('click', null, bs.doAddItemToCart);
    $('#delItem2CartBtn').on('click', null, bs.doDelItemToCart);
    $('.listGobkBtn').on('click', null, bs.rtnToSrch);

    $('#biblioListDiv .goPrevBtn').on('click', null, function () {
      bs.goPrevPage(bs.previousPageItem);
    });
    $('#biblioListDiv .goNextBtn').on('click', null, function () {
      bs.goNextPage(bs.nextPageItem);
    });
    $('#biblioListDiv .goNextBtn').disable();
    $('#biblioListDiv .goPrevBtn').disable();

    // --- Single biblio display bindings ---
    $('#photoAddBtn').on('click', null, function () {
      bs.doPhotoAdd(bs.theBiblio);
    });
    $('#photoEditBtn').on('click', null, function () {
      $('#updtFotoBtn').show();
      bs.doPhotoEdit(bs.theBiblio);
    });
    $('#biblioEditBtn').on('click', null, function () {
      ie.doItemEdit(bs.theBiblio);
    });
    $('#biblioDeleteBtn').on('click', null, function () {
      idis.doItemDelete(bs.theBiblio);
    });
    $('#marcBtn').on('click', null, function () {
      var marcFlds = $('#biblioDiv td.filterable');
      if (marcFlds.is(':hidden')) {
        marcFlds.show();
        $('#marcBtn').val(bs.hideMarc);
      } else {
        marcFlds.hide();
        $('#marcBtn').val(bs.showMarc);
      }
    });
    $('.bibGobkBtn').on('click', null, function () {
      if (bs.multiMode) {
        bs.rtnToList();
      } else {
        bs.rtnToSrch();
      }
      $('#cart_result').html('');
    });

    // --- Item editor bindings ---
    $('#itemSubmitBtn').on('click', null, bs.doItemUpdate)
      .val('<?php echo $tUpdate; ?>');

    $('#copyCancelBtn').on('click', null, function () {
      idis.fetchCopyInfo();
      bs.rtnToBiblio();
    });

    $('.itemGobkBtn').on('click', null, function () {
      $('#itemEditorDiv').hide();
      $('#biblioDiv').show();
    });

    // --- Initialise UI and load data ---
    bs.resetForms();
    bs.fetchOpts();
    bs.fetchCrntMbrInfo();

    bs.fetchSiteList();           // also inits itemDisplayJs
    bs.fetchStatusCdsList($('#status_cd'));
    bs.fetchMaterialList();
    bs.fetchCollectionList();
    bs.fetchAudienceList();

    bs.fetchMediaDisplayInfo();
    bs.fetchMediaLineCnt();
    bs.fetchMediaIconUrls();
  },

  // =========================================================================
  // Widget / plugin init hook (extension point)
  // =========================================================================
  initWidgets: function () {
  },

  // =========================================================================
  // resetForms — return to initial search view
  // =========================================================================
  resetForms: function () {
    var $div = function (id) { return $('#' + id); };

    $('#advancedSrch').hide();
    $('#marcTagsRow').hide();
    $div('crntMbrDiv').hide();
    $div('searchDiv').show();
    obib.hideMsg('now');
    $div('biblioDiv').hide();
    $div('biblioListDiv').hide();
    $div('itemEditorDiv').hide();
    $div('copyEditorDiv').hide();
    $div('photoEditorDiv').hide();

    bs.multiMode = false;
    bs.checkPhraseSrchBtn();
    bs.checkBarcdSrchBtn();

    $('#marcBtn').val(bs.showMarc);
    if (opacMode) {
      $('#barcodeSearch').hide();
    }
    $('#ph_searchText').focus();
  },

  // =========================================================================
  // Navigation helpers
  // =========================================================================
  rtnToSrch: function () {
    $('tbody#biblio').html('');
    $('tbody#copies').html('');
    obib.hideMsg();
    $('#editRsltMsg').html('');
    $('#biblioDiv').hide();
    $('#biblioListDiv').hide();
    $('#searchDiv').show();
    $('#itemEditorDiv').hide();
    $('#copyEditorDiv').hide();
    $('#photoEditorDiv').hide();
    bs.checkPhraseSrchBtn();
    bs.checkBarcdSrchBtn();
  },

  rtnToList: function () {
    obib.hideMsg();
    $('#editRsltMsg').html('');
    $('#biblioDiv').hide();
    $('#biblioListDiv').show();
    $('#searchDiv').hide();
    $('#itemEditorDiv').hide();
    $('#copyEditorDiv').hide();
    $('#photoEditorDiv').hide();
  },

  rtnToBiblio: function () {
    obib.hideMsg();
    $('#biblioDiv').show();
    $('#biblioListDiv').hide();
    $('#searchDiv').hide();
    $('#itemEditorDiv').hide();
    $('#copyEditorDiv').hide();
    $('#photoEditorDiv').hide();
  },

  // =========================================================================
  // Search button enable/disable helpers
  // =========================================================================
  checkPhraseSrchBtn: function () {
    var hasText = $('#ph_searchText').val().length > 0;
    $('#phraseSrchBtn').prop('disabled', !hasText)
      .css('color', hasText ? bs.srchBtnClr : '#888888');
  },

  checkBarcdSrchBtn: function () {
    var hasText = $('#bc_searchBarcd').val().length > 0;
    $('#barcdSrchBtn').prop('disabled', !hasText)
      .css('color', hasText ? bs.srchBtnClr : '#888888');
  },

  formatBarcode: function () {
    var barcd = $.trim($('#bc_searchBarcd').val());
    barcd = flos.pad(barcd, bs.opts.barcdWidth, '0');
    $('#bc_searchBarcd').val(barcd);
  },

  // =========================================================================
  // doAltStart — alternative entry point (triggered from PHP params)
  // =========================================================================
  doAltStart: function () {
    <?php if (isset($_REQUEST['barcd'])): ?>
      $('#bc_searchBarcd').val(<?php echo json_encode($_REQUEST['barcd']); ?>);
      bs.doBarcdSearch();
    <?php elseif (isset($_REQUEST['bibid'])): ?>
      bs.doBibidSearch(<?php echo (int) $_REQUEST['bibid']; ?>);
    <?php elseif (isset($_REQUEST['searchText'])): ?>
      $('#ph_searchText').val(<?php echo json_encode($_REQUEST['searchText']); ?>);
      $('#ph_searchType').val(<?php echo json_encode($_REQUEST['searchType'] ?? ''); ?>);
      bs.doPhraseSearch();
    <?php endif; ?>
  },

  // =========================================================================
  // Data fetchers — populate dropdowns and options
  // =========================================================================
  fetchOpts: function () {
    bs.opts.showBiblioPhotos = '<?php echo $showPhotos; ?>';
    bs.opts.barcdWidth       = <?php echo $barcdWidth; ?>;
    bs.opts.current_site     = '<?php echo $curSite; ?>';
  },

  fetchCrntMbrInfo: function () {
    $.post(bs.url, { mode: 'getCrntMbrInfo' }, function (data) {
      $('#crntMbrDiv').empty().html(data).show();
    }, 'json');
  },

  fetchMaterialList: function () {
    list.getMaterialList($('#srchMediaTypes'), function () {
      $('#srchMediaTypes').prepend('<option value="all" selected="selected"><?php echo $tAll; ?></option>');
    });
  },

  fetchCollectionList: function () {
    list.getCollectionList($('#srchCollections'), function () {
      $('#srchCollections').prepend('<option value="all" selected="selected"><?php echo $tAll; ?></option>');
    });
  },

  fetchAudienceList: function () {
    $.post(bs.listSrvr, { mode: 'getAudienceList' }, function (data) {
      var html = '';
      for (var n in data) {
        if (Object.prototype.hasOwnProperty.call(data, n)) {
          html += '<option value="' + data[n] + '">' + data[n] + '</option>';
        }
      }
      $('#itemEditColls').html(html);
      html = '<option value="all" selected="selected"><?php echo $tAll; ?></option>' + html;
      $('#audienceLevel').html(html);
    }, 'json');
  },

  fetchStatusCdsList: function (where) {
    list.getStatusCds(where);
  },

  fetchSiteList: function () {
    $.post(bs.listSrvr, { mode: 'getSiteList' }, function (data) {
      bs.siteList = data;
      var html = '';
      for (var n in data) {
        if (Object.prototype.hasOwnProperty.call(data, n)) {
          html += '<option value="' + n + '">' + data[n] + '</option>';
        }
      }
      $('#copySite').html(html);
      html = '<option value="all" selected="selected"><?php echo $tAll; ?></option>' + html;
      $('#srchSites').html(html);

      idis.init(bs.opts, bs.siteList);
      ie.init(bs.opts, bs.siteList);

      bs.doAltStart();
    }, 'json');
  },

  fetchMediaDisplayInfo: function () {
    $.post(bs.url, { mode: 'getMediaDisplayInfo', howMany: 'all' }, function (response) {
      bs.displayInfo = response;
    }, 'json');
  },

  fetchMediaIconUrls: function () {
    $.post(bs.listSrvr, { mode: 'getMediaIconUrls' }, function (response) {
      bs.mediaIconUrls = response;
    }, 'json');
  },

  fetchMediaLineCnt: function () {
    $.post(bs.url, { mode: 'getMediaLineCnt' }, function (response) {
      bs.mediaLineCnt = response;
    }, 'json');
  },

  // =========================================================================
  // Search methods
  // =========================================================================
  doBibidSearch: function (bibid) {
    bs.srchType = 'bibid';
    $('p.error').html('').hide();

    $.post(bs.url, '&mode=doBibidSearch&bibid=' + bibid, function (jsonInpt) {
      if (typeof jsonInpt !== 'object') {
        obib.showMsg(jsonInpt);
      } else {
        bs.biblio = jsonInpt;
        if (!bs.biblio.hdr) {
          obib.showMsg('<?php echo $tNothingByBarc; ?>');
        } else {
          idis.showOneBiblio(bs.biblio);
        }
      }
      $('#searchDiv').hide();
      $('#biblioDiv').show();
    }, 'json').fail(function () {
      obib.showMsg('<?php echo $tNothingFound; ?>');
      $('#searchDiv').hide();
      $('#biblioDiv').show();
    });
    return false;
  },

  doBarcdSearch: function (e) {
    var barcd = $.trim($('#searchBarcd').val());
    barcd = flos.pad(barcd, bs.opts.barcdWidth, '0');
    $('#searchBarcd').val(barcd);

    bs.srchType = 'barCd';
    $('p.error').html('').hide();

    var params = $('#barcodeSearch').serialize() + '&mode=doBarcdSearch';
    $.post(bs.url, params, function (jsonInpt) {
      if (jsonInpt && jsonInpt.message) {
        obib.showMsg(jsonInpt.message);
        return;
      }
      bs.biblio = jsonInpt;
      if (bs.biblio && bs.biblio.hdr !== null && bs.biblio.hdr !== undefined) {
        bs.multiMode = false;
        idis.showOneBiblio(bs.biblio);
      } else {
        obib.showMsg('<?php echo $tNothingFound; ?>');
        bs.rtnToSrch();
      }
      $('#searchDiv').hide();
      $('#biblioDiv').show();
    }, 'json').fail(function () {
      obib.showMsg('<?php echo $tNothingFound; ?>');
      $('#searchDiv').hide();
      $('#biblioDiv').show();
    });
    return false;
  },

  doPhraseSearch: function (e, firstItem) {
    $('#biblioListDiv').show();
    $('#searchDiv').hide();
    $('#resultsArea').html('');

    var searchType = $('#ph_searchType option:selected').val();
    var searchText = $('#ph_searchText').val();
    $('#srchRsltTitl').html(searchText);

    // Special handling for ID search
    if (searchType === 'id') {
      if (e) { e.preventDefault(); }
      bs.doBibidSearch(searchText);
      return false;
    }

    // "Searching..." placeholder
    $('#srchRslts').html(
      '<p class="error">' +
      ' <img src="../images/512-1.webp" width="36" />' +
      ' <?php echo $tSearching; ?>' +
      '</p>\n'
    );

    $('.rsltQuan').html('');
    if (firstItem === null || firstItem === undefined) {
      firstItem = 0;
    }
    bs.srchType = 'phrase';
    var params = $('#phraseSearch').serialize() + '&mode=doPhraseSearch&firstItem=' + firstItem;

    $.post(bs.url, params, function (jsonInpt) {
      var biblioList = jsonInpt;

      if (!biblioList || biblioList.length === 0 || $.trim(jsonInpt) === '[]') {
        // No hits
        bs.multiMode = false;
        $('#srchRslts').html('<p class="error"><?php echo $tNothingFound; ?></p>');
        $('#biblioListDiv .goNextBtn').disable();
        $('#biblioListDiv .goPrevBtn').disable();
      } else if (biblioList.length === 2 && firstItem === 0) {
        // Single hit (first record is query info, second is the biblio)
        bs.multiMode = false;
        bs.biblio = JSON.parse(biblioList[1]);
        idis.showOneBiblio(bs.biblio);
      } else {
        // Multiple hits
        bs.multiMode = true;
        bs.showList(firstItem, biblioList);
      }
    }, 'json').fail(function () {
      // JSON parse failed — likely a PHP warning/notice in the response
      $('#srchRslts').html('<p class="error"><?php echo $tNothingFound; ?></p>');
      $('#biblioListDiv .goNextBtn').disable();
      $('#biblioListDiv .goPrevBtn').disable();
    });
    return false;
  },

  // =========================================================================
  // getPhoto — fetch and display a biblio thumbnail
  // =========================================================================
  getPhoto: function (bibid, dest) {
    $.post(bs.url, { mode: 'getPhoto', bibid: bibid }, function (data) {
      if (data && data.length > 0) {
        var foto = data[0];
        $(dest).html($('<img src="' + foto.url + '" class="biblioImage hover">'));
      }
    }, 'json');
  },

  // =========================================================================
  // showList — build the multi-result search list table
  // =========================================================================
  showList: function (firstItem, biblioData) {
    if (firstItem === null || firstItem === undefined) {
      firstItem = 0;
    }

    // Parse query metadata (first record)
    var queryInfo = JSON.parse(biblioData[0]);
    var perPage       = parseInt(queryInfo.itemsPage, 10);
    var ttlNum        = parseInt(queryInfo.totalNum, 10);
    var lastItem      = parseInt(queryInfo.lastItem, 10);
    var modFirstItem  = parseInt(queryInfo.firstItem, 10) + 1;
    firstItem         = parseInt(queryInfo.firstItem, 10);

    // Clone list without the info record
    var biblioList = biblioData.slice(1);

    // Build sort index
    var sortKeys = ['Call Number', 'Title', 'Author'];
    var biblioNdx = [];
    for (var i = 0; i < biblioList.length; i++) {
      var entry = { index: i };
      var biblio = JSON.parse(biblioList[i]);
      var marc = biblio.marc;
      if (marc) {
        for (var j = 0; j < marc.length; j++) {
          var key = marc[j].lbl;
          if (sortKeys.indexOf(key) !== -1) {
            entry[key] = marc[j].value;
          }
        }
      }
      biblioNdx.push(entry);
    }

    // Sort
    var sortBy = $('#sortBy option:selected').val();
    biblioNdx.sort(bs.by(sortBy, true));

    // Display result count
    $('.rsltQuan').html(' ' + ttlNum + ' <?php echo $tItems; ?>(' + modFirstItem + '-' + lastItem + ') ');
    bs.biblio = [];

    var $srchRslts = $('#listTbl tbody#srchRslts');
    $srchRslts.html('');

    for (var seq = 0; seq < biblioNdx.length; seq++) {
      var ndx = biblioNdx[seq].index;
      var biblio = JSON.parse(biblioList[ndx]);

      if (!biblio.hdr) {
        continue;
      }

      var hdr  = biblio.hdr;
      var cpys = biblio.cpys ? biblio.cpys.length : 0;
      var avail = cpys > 0 ? '\u2705' : '\u274C'; // ✅ : ❌

      idis.crntBibid = hdr.bibid;
      bs.biblio[hdr.bibid] = biblio;

      var html = '<tr class="listItem">\n';

      // --- Left column: visual / photo ---
      html += '<td style="width: 200px;">\n';
      html += '<div class="itemVisual">\n';

      if (bs.opts.showBiblioPhotos === 'Y' && hdr.bibid !== undefined) {
        html += '<div class="photos" id="photo_' + hdr.bibid + '">\n';
        html += '<img src="../images/shim.gif" class="biblioImage noHover" height="50px" width="50px" />\n';
        html += '</div>\n';
        bs.getPhoto(hdr.bibid, '#photo_' + hdr.bibid);
      }

      html += '<div class="dashBds">\n';
      html += '<div class="dashBdsA">';
      html += '<p><b>\uD83D\uDD16Bibid: <mark>' + hdr.bibid + '</mark> <br>' + avail + 'Copies: ' + cpys + '</b></p>';
      html += '</div>\n';
      html += '<div class="dashBdsB"></div>\n';
      html += '<div class="dashBdsC">';
      html += '<input type="hidden" value="' + hdr.bibid + '" />\n';
      html += '<input type="button" style="margin-top: 20px;" class="moreBtn" value="\uD83D\uDD0D View Info" />\n';
      html += '</div>';
      html += '</div>\n';   // .dashBds
      html += '</div></td>'; // .itemVisual

      // --- Right column: biblio data lines ---
      var marc = biblio.marc;
      if (!marc) {
        html += '</tr>\n';
        $srchRslts.append(html);
        continue;
      }

      var lines = [];
      $.each(marc, function (i, fld) {
        lines.push((fld.value || '').trim());
      });

      var N = bs.mediaLineCnt[hdr.material_cd];
      var emojis = [
        '\uD83D\uDD16<b> Call Number: </b>',
        '\uD83D\uDC68\u200D\uD83D\uDCBC<b> Author: </b>',
        '\uD83D\uDCD9<b> Title: </b>'
      ];

      html += '<td id="itemInfo">\n';
      for (var k = 0; k < N && k < lines.length; k++) {
        if (lines[k]) {
          var emoji = emojis[k] || '';
          html += '<p class="searchListItem">' + emoji + lines[k] + '</p>\n';
        }
      }
      html += '</td></tr>\n';

      $srchRslts.append(html);
    }

    obib.reStripe2('listTbl', 'odd');

    // Bind "more detail" buttons (created dynamically, no duplicate risk)
    $('.moreBtn').on('click', null, bs.getPhraseSrchDetails);

    // Pagination buttons
    if (firstItem >= perPage) {
      bs.previousPageItem = firstItem - perPage;
      $('#biblioListDiv .goPrevBtn').enable();
    } else {
      $('#biblioListDiv .goPrevBtn').disable();
    }
    if ((perPage + firstItem <= lastItem) && (ttlNum !== lastItem)) {
      bs.nextPageItem = perPage + firstItem;
      $('#biblioListDiv .goNextBtn').enable();
    } else {
      $('#biblioListDiv .goNextBtn').disable();
    }

    $('#biblioListDiv').show();
    $('#biblioDiv').hide();
    $('#searchDiv').hide();
  },

  // =========================================================================
  // by — generic array-of-objects sort comparator
  // based on http://stackoverflow.com/a/979325/2502532
  // =========================================================================
  by: function (field, reverse, primer) {
    var key = function (x) {
      return primer ? primer(x[field]) : x[field];
    };
    return function (a, b) {
      var A = key(a), B = key(b);
      return ((A < B) ? -1 : ((A > B) ? 1 : 0)) * (reverse ? -1 : 1);
    };
  },

  // =========================================================================
  // Pagination
  // =========================================================================
  goNextPage: function (firstItem) {
    $('#biblioListDiv .goNextBtn').disable();
    bs.doPhraseSearch(null, firstItem);
  },
  goPrevPage: function (firstItem) {
    $('#biblioListDiv .goPrevBtn').disable();
    bs.doPhraseSearch(null, firstItem);
  },

  // =========================================================================
  // Cart / tagging
  // =========================================================================
  getPhraseSrchDetails: function () {
    var bibid = $(this).prev().val();
    idis.showOneBiblio(bs.biblio[bibid]);
  },

  doDelItemToCart: function () {
    var bibid = document.getElementById('theBibId').textContent.trim();
    var params = 'mode=delToCart&name=bibid&tab=catalog&id[]=' + bibid;
    $.post(bs.url, params, function (response) {
      if ($('#cart_result').length === 0) { return; }
      var match = response.match(/\$rslt:\s*(.*)/i);
      var rslt = match ? match[1].trim() : null;
      if (rslt === '1') {
        $('#cart_result').html('<h4>✅ Item untagged! ✅</h4>');
      } else {
        $('#cart_result').html('<h4>⚠️ Nothing to untag! (Not present on Tagged Items) ⚠️</h4>');
      }
    }, 'text');
  },

  doAddItemToCart: function () {
    var bibid = document.getElementById('theBibId').textContent.trim();
    var params = 'mode=addToCart&name=bibid&tab=catalog&id[]=' + bibid;
    $.post(bs.url, params, function (response) {
      if ($('#cart_result').length === 0) { return; }
      var match = response.match(/\$rslt:\s*(.*)/i);
      var rslt = match ? match[1].trim() : null;
      if (rslt === '1') {
        $('#cart_result').html('<h4>⚠️ Already tagged! ⚠️</h4>');
      } else {
        $('#cart_result').html('<h4>✅ Item tagged! <i>(See Tagged Items)✅</i></h4>');
      }
    }, 'text');
  },

  // =========================================================================
  // makeDueDateStr — calculate due date from checkout date
  // =========================================================================
  makeDueDateStr: function (dtOut, daysDueBack) {
    if (daysDueBack === null || daysDueBack === undefined) {
      daysDueBack = 0;
    }
    var parts = dtOut.split(' ');
    var dat = parts[0].split('-');
    var dateOut = new Date(dat[0], dat[1] - 1, dat[2]);
    dateOut.setDate(dateOut.getDate() + daysDueBack);
    return dateOut.toDateString();
  },

  // =========================================================================
  // findMarcField / findMarcFieldSet — MARC field lookup (no eval!)
  // =========================================================================
  findMarcField: function (biblio, tag) {
    if (!biblio || !biblio.data) { return null; }
    for (var i = 0; i < biblio.data.length; i++) {
      var tmp = (typeof biblio.data[i] === 'string')
        ? JSON.parse(biblio.data[i])
        : biblio.data[i];
      if (tmp.marcTag === tag) {
        return tmp;
      }
    }
    return null;
  },

  findMarcFieldSet: function (biblio, tag) {
    var fldSet = [];
    if (!biblio || !biblio.data) { return fldSet; }
    for (var i = 0; i < biblio.data.length; i++) {
      var tmp = (typeof biblio.data[i] === 'string')
        ? JSON.parse(biblio.data[i])
        : biblio.data[i];
      if (tmp.marcTag === tag) {
        fldSet.push(tmp);
      }
    }
    return fldSet;
  },

  // =========================================================================
  // Photo editing
  // =========================================================================
  doPhotoEdit: function () {
    if (!wc.url) { wc.init(); }

    $('#fotoHdr').val('<?php echo $tEditFoto; ?>');
    $('#deltFotoBtn').show();
    $('#addFotoBtn').hide();
    $('#updtFotoBtn').show();
    $('#fotoMsg').hide();
    $('#fotoMode').val('updatePhoto');
    $('#fotoSrce').attr({ required: false, 'aria-required': false });
    bs.showPhotoForm();
  },

  doPhotoAdd: function () {
    if (!wc.url) { wc.init(); }

    $('#updtFotoBtn').hide();
    $('#fotoHdr').val('<?php echo $tAddFoto; ?>');
    $('#deltFotoBtn').hide();
    $('#addFotoBtn').show();
    $('#fotoMsg').hide();
    $('#fotoMode').val('addNewPhoto');
    $('#fotoSrce').attr({ required: true, 'aria-required': true });
    bs.showPhotoForm();
  },

  showPhotoForm: function () {
    if (!wc.url) { wc.init(); }

    $('#biblioDiv').hide();
    $('#fotoSrce').val('');
    $('#fotoBibid').val(idis.crntBibid);

    if (idis.crntFoto === null || idis.crntFoto === undefined) {
      $('#fotoEdLegend').html('<?php echo $tEnterFoto; ?>');
      $('#fotoName').val(idis.crntBibid + '.jpg');
      wc.eraseImage();
    } else {
      $('#fotoEdLegend').html('<?php echo $tCoverFoto; ?>: ' + idis.crntTitle);
      $('#fotoName').val('<?php echo $uploadDir; ?>' + idis.crntFoto.url);
      wc.showImage($('#fotoName').val());
    }

    $('.gobkFotoBtn').on('click', null, bs.rtnToBiblio);
    $('#photoEditorDiv').show();
  },

  // =========================================================================
  // Item editing
  // =========================================================================
  doItemEdit: function (biblio) {
    ie.doItemEdit(biblio);
  },

  doItemUpdate: function (e) {
    e.preventDefault();
    e.stopPropagation();
    var params = '&mode=updateBiblio&' + $('#biblioEditForm').not('.online').serialize();
    $.post(ie.url, params, function (response) {
      if (response === '!!success!!') {
        $('#msgDiv').html('<?php echo $tUpdateSuccess; ?>');
        $('#itemEditorDiv').hide();
        setTimeout(function () {
          $('#msgDiv').show().hide(3000);
        }, 3000);
        $('#msgDiv').show();
        if (bs.srchType === 'barCd') {
          bs.doBarcdSearch();
        } else if (bs.srchType === 'phrase') {
          bs.doPhraseSearch();
        }
      } else {
        $('#itemRsltMsg').html(response);
      }
    }, 'text');
    return false;
  },

  // =========================================================================
  // Copy creation
  // =========================================================================
  doNewCopy: function (e) {
    e.stopPropagation();
    $('#biblioDiv').hide();
    $('#copyBibid').val(idis.crntBibid);
    $('#copySite').val(bs.opts.current_site);

    $('#copyEditorDiv').show();
    ced.bibid = idis.crntBibid;
    ced.doCopyNew(e);
    e.preventDefault();
  }
};

// ---------------------------------------------------------------------------
// Kick off on DOM ready
// ---------------------------------------------------------------------------
$(document).ready(bs.init);

</script>