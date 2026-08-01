<?php
/**
 * OpenBiblio Catalog Search Forms
 *
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Refactored for PHP 8.3 compatibility and improved maintainability. -F. Tumulak
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 1. Dependencies
// ---------------------------------------------------------------------------
require_once ('../shared/common.php');
require_once REL(__FILE__, '../functions/inputFuncs.php');

// ---------------------------------------------------------------------------
// 2. Determine active tab and page title
// ---------------------------------------------------------------------------
$tab = strtolower((string) ($_REQUEST['tab'] ?? ''));

$title = match ($tab) {
  'user', 'opac' => T('Library Catalog'),
  'rpt'          => T('ReportSelection'),
  default        => T('Existing Items'),
};

if ($tab === '') {
  $tab = 'cataloging';
}

// ---------------------------------------------------------------------------
// 3. Navigation metadata
// ---------------------------------------------------------------------------
$nav              = 'localSearch';
$menu             = $tab . '/search/catalog';
$focus_form_name  = 'phraseSearch';
$focus_form_field = 'ph_searchText';

// ---------------------------------------------------------------------------
// 4. Auth check (skip for OPAC/public tab)
// ---------------------------------------------------------------------------
if ($tab !== 'opac') {
  require_once REL(__FILE__, '../shared/logincheck.php');
}

// ---------------------------------------------------------------------------
// 5. Navigation breadcrumbs and page header
// ---------------------------------------------------------------------------
Nav::node($menu, T('Print Catalog'), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=cataloging');
Nav::node($menu, T('MARC Output'), '../shared/layout.php?name=marc&rpt=Report&tab=cataloging');
Page::header(['nav' => "{$tab}/{$nav}", 'title' => $title]);

	// This will enable / disable the delete copies button --F.Tumulak
	// This will get triggered by itemDisplayJs.php, keyword: deltBtn
	$authClass = (isset($_SESSION["hasReportsAuth"]) && $_SESSION["hasReportsAuth"]) ? '' : ' hidden';

// ---------------------------------------------------------------------------
// 6. Derive booleans used throughout the template
// ---------------------------------------------------------------------------
$isCatalogTab   = ($tab === 'cataloging');
$isReportTab    = ($tab === 'rpt');
$hasCatalogAuth = !empty($_SESSION['hasCatalogAuth']);
$hasReportsAuth = !empty($_SESSION['hasReportsAuth']);
$hasCircAuth    = !empty($_SESSION['hasCircAuth']);
$showItemPhotos = ($_SESSION['show_item_photos'] ?? 'N') === 'Y';
$showDetailOpac = ($_SESSION['show_detail_opac'] ?? 'N') === 'Y';

// Only show "Add New Copy" when on catalog tab and the user is not a
// circulation-only user without catalog privileges.
$showAddNewCopy = $isCatalogTab && !($hasCircAuth && !$hasCatalogAuth);

// ---------------------------------------------------------------------------
// 7. Helper: escape a translated string for use inside an HTML attribute
// ---------------------------------------------------------------------------
$escAttr = static fn(string $key): string => H(T($key));

// ---------------------------------------------------------------------------
// 8. Output the form (template / view layer)
// ---------------------------------------------------------------------------
?>
<div id="crntMbrDiv">to be filled by server</div>

<p id="whereAmI" class="note"></p>
<p id="msgDiv" style="text-align: center; font-weight: bold;"></p>

<!-- ======================================================================== -->
<div id="searchDiv">

  <!-- ---- barcode search ---- -->
  <form role="form" id="barcodeSearch" name="barcodeSearch" method="post">
    <fieldset>
      <legend><?php echo $escAttr('Find Item by Barcode'); ?></legend>
      <label for="bc_searchBarcd" style="margin-left: 15px;"><?php echo $escAttr('Barcode'); ?>:</label>
      <input type="text" id="bc_searchBarcd" name="searchBarcd" size="20" value=""
             inputmode="numeric" pattern="\d*" maxlength="13"
             oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" />
      <input type="submit" id="barcdSrchBtn" name="barcdSrchBtn" value="<?php echo $escAttr('Search'); ?>" class="srchByBarcdBtn" />
      <input type="hidden" id="bc_searchType" name="searchType" value="barcodeNmbr" />
      <input type="hidden" id="bc_sortBy" name="sortBy" value="default" />
    </fieldset>
  </form>

  <!-- ---- phrase / keyword search ---- -->
  <form role="form" id="phraseSearch" name="phraseSearch" method="post">
    <fieldset>
      <legend><?php echo $escAttr('Search Catalog'); ?></legend>
      <table>
        <tbody id="mainTxtSrch">
        <tr>
          <td colspan="3">
            <select id="ph_searchType" name="searchType">
              <option value="title"><?php echo $escAttr('Title'); ?></option>
              <option value="author"><?php echo $escAttr('Author'); ?></option>
              <option value="subject"><?php echo $escAttr('Subject'); ?></option>
              <option value="keyword" selected><?php echo $escAttr('Keyword'); ?></option>
              <option value="series"><?php echo $escAttr('Series'); ?></option>
              <option value="publisher"><?php echo $escAttr('Publisher'); ?></option>
              <option value="callno"><?php echo $escAttr('Call Number'); ?></option>
              <option value="id"><?php echo $escAttr('bibid'); ?></option>
            </select>
            <input type="text" value="" id="ph_searchText" name="searchText" size="20" maxlength="256" />
            <input type="submit" id="phraseSrchBtn" name="phraseSrchBtn" value="<?php echo $escAttr('Search'); ?>" class="phraseSrchBtnBtn" />
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <input id="tab" name="tab" type="hidden" value="<?php echo H($tab); ?>" />
            <input id="lookup" name="lookup" type="hidden" value="" />
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <label for="advanceQ"><?php echo $escAttr('Advanced Search?'); ?></label>
            <input id="advanceQ" name="advanceQ" type="checkbox" value="Y" />
          </td>
        </tr>
        </tbody>

        <!-- ---- advanced search (hidden by default) ---- -->
        <tbody id="advancedSrch">
        <tr>
          <td nowrap="true" colspan="3">
            <label for="sortBy"><?php echo $escAttr('Sort by'); ?>: </label>
            <select id="sortBy" name="sortBy">
              <option value="Author"><?php echo $escAttr('Author'); ?></option>
              <option value="Call Number" selected><?php echo $escAttr('Call Number'); ?></option>
              <option value="Title"><?php echo $escAttr('Title'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <fieldset>
              <legend><?php echo $escAttr('Limit Search Results'); ?></legend>
              <table border="0">
                <tr class="searchRow">
                  <td><label for="srchMediaTypes"><?php echo $escAttr('Media Type'); ?>: </label></td>
                  <td><select id="srchMediaTypes" name="materialCd"></select></td>
                </tr>
                <tr class="searchRow">
                  <td><label for="srchCollections"><?php echo $escAttr('Collection'); ?>: </label></td>
                  <td><select id="srchCollections" name="collectionCd"></select></td>
                </tr>
                <tr class="searchRow">
                  <td><label for="audienceLevel"><?php echo $escAttr('Audience Level'); ?>: </label></td>
                  <td>
                    <select id="audienceLevel" name="audienceLevel">
                      <option value="all" selected><?php echo $escAttr('All'); ?></option>
                    </select>
                  </td>
                </tr>
                <tr class="searchRow">
                  <td><label for="srchSites"><?php echo $escAttr('Search Site'); ?>:</label></td>
                  <td>
                    <select name="srchSites" id="srchSites">
                      <option value="all" selected><?php echo $escAttr('All'); ?></option>
                      <option>to be filled by server</option>
                    </select>
                  </td>
                </tr>
                <tr class="searchRow">
                  <td><label><?php echo $escAttr('Production Date'); ?>:</label><br /></td>
                  <td>
                    <label for="from"><?php echo $escAttr('From Year'); ?>:</label>
                    <input id="from" name="from" type="text" inputmode="numeric" size="4"
                           minlength="4" maxlength="4" pattern="\d{4}" />
                    <br />
                    <label for="to"><?php echo $escAttr('To Year'); ?>:</label>
                    <input id="to" name="to" type="text" inputmode="numeric" size="4"
                           minlength="4" maxlength="4" pattern="\d{4}" />
                  </td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
        </tbody>
      </table>
    </fieldset>
  </form>
</div>

<!-- ======================================================================== -->
<div id="biblioListDiv">
  <h5><?php echo $escAttr('SearchResults'); ?> &quot;<span id="srchRsltTitl"></span>&quot;</h5>
  <table>
  <tr>
    <td colspan="3">
      <ul class="pagBtns">
        <li><input type="button" class="listGobkBtn" value="<?php echo $escAttr('Go Back'); ?>" /></li>
        <li>
          <input type="button" class="goPrevBtn PgBtn" value="<?php echo $escAttr('Previous Page'); ?>" />
          <span class="rsltQuan"></span>
          <input type="button" class="goNextBtn PgBtn" value="<?php echo $escAttr('Next Page'); ?>" />
        </li>
      </ul>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <fieldset>
        <span id="resultsArea"></span>
        <fieldset>
          <table id="listTbl">
            <tbody id="srchRslts" class="striped">
            </tbody>
          </table>
        </fieldset>
        <?php if ($showDetailOpac): ?>
        <ul id="flagInfo">
          <li>✅ <?php echo $escAttr('Available'); ?></li>
          <li>❌ <?php echo $escAttr('On loan/not available'); ?></li>
          <li><p>📌 Press CTRL+F5 on your browser to refresh image (In case no response from thumbnail add or remove )</p></li>
        </ul>
        <?php endif; ?>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <ul class="pagBtns">
        <li><input type="button" class="listGobkBtn" value="<?php echo $escAttr('Go Back'); ?>" /></li>
        <li>
          <input type="button" class="goPrevBtn PgBtn" value="<?php echo $escAttr('Previous Page'); ?>" />
          <span class="rsltQuan"></span>
          <input type="button" class="goNextBtn PgBtn" value="<?php echo $escAttr('Next Page'); ?>" />
        </li>
      </ul>
    </td>
  </tr>
  </table>
</div>

<!-- ======================================================================== -->
<div id="biblioDiv">
  <ul class="btnRow">
    <?php if (!$isReportTab): ?>
      <li><input type="button" class="bibGobkBtn" value="<?php echo $escAttr('Go Back'); ?>" /></li>
    <?php endif; ?>
    <li><input type="button" id="marcBtn" value="" /></li>
    <?php if ($hasCatalogAuth && $isCatalogTab): ?>
      <li><input type="button" id="addItem2CartBtn" value="<?php echo $escAttr('Tag item'); ?>" /></li>
      <li><input type="button" id="delItem2CartBtn" value="<?php echo $escAttr('Un-tag item'); ?>" /></li>
      <li><input type="button" id="biblioEditBtn" value="<?php echo $escAttr('Edit This Item'); ?>" /></li>
      <?php if ($showItemPhotos): ?>
        <li><input type="button" id="photoEditBtn" value="<?php echo $escAttr('Edit This Photo'); ?>" /></li>
        <li><input type="button" id="photoAddBtn" value="<?php echo $escAttr('Add New Photo'); ?>" /></li>
      <?php endif; ?>
      <?php if ($hasReportsAuth): ?>
        <li><input type="button" id="biblioDeleteBtn" value="<?php echo $escAttr('Delete This Item'); ?>" /></li>
      <?php endif; ?>
    <?php endif; ?>
  </ul>
  <div id="cart_result"></div>

  <?php require_once REL(__FILE__, '../catalog/itemDisplayForm.php'); ?>

  <ul class="btnRow">
    <?php if (!$isReportTab): ?>
      <li><input type="button" class="bibGobkBtn" value="<?php echo $escAttr('Go Back'); ?>" /></li>
    <?php endif; ?>
    <?php if ($showAddNewCopy): ?>
      <li><input type="button" id="addNewBtn" class="button" value="<?php echo $escAttr('Add New Copy'); ?>" /></li>
    <?php endif; ?>
  </ul>
</div>

<!-- ======================================================================== -->
<div id="itemEditorDiv">
  <form role="form" id="biblioEditForm" name="biblioEditForm">
    <h5 id="reqdNote">*<?php echo $escAttr('Required note'); ?></h5>
    <div class="btnRow flexBoxed">
      <input type="button" class="itemGobkBtn leftBtn" value="<?php echo $escAttr('Go Back'); ?>" />
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="button" id="onlnDoneBtn" class="button rightBtn" value="<?php echo $escAttr('Search Complete'); ?>" />
    </div>

    <?php require_once REL(__FILE__, '../catalog/itemEditorForm.php'); ?>

    <input type="button" id="itemSubmitBtn" value="<?php echo $escAttr('Submit'); ?>" />
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="<?php echo $escAttr('Go Back'); ?>" class="itemGobkBtn" />
  </form>
</div>

<!-- ======================================================================== -->
<div id="copyEditorDiv">
  <?php require_once REL(__FILE__, '../catalog/copyEditorForm.php'); ?>
</div>

<!-- ======================================================================== -->
<?php if ($isCatalogTab): ?>
<div id="photoEditorDiv">
  <?php require_once REL(__FILE__, '../catalog/photoEditorForm.php'); ?>

  <ul class="btnRow">
    <li><input type="button" class="gobkFotoBtn" value="<?php echo $escAttr('Go Back'); ?>" /></li>
    <li><input type="submit" id="addFotoBtn" value="<?php echo $escAttr('Add New'); ?>" /></li>
    <li><input type="button" id="deltFotoBtn" value="<?php echo $escAttr('Delete'); ?>" /></li>
  </ul>
</div>
<?php endif; ?>

<!-- ======================================================================== -->
<?php
require_once REL(__FILE__, '../shared/footer.php');
require_once REL(__FILE__, '../catalog/srchJs.php');
?>
</body>
</html>