<?php
/**
 * Back-end API for Existing Biblio Management
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
// 1. Dependencies
// ---------------------------------------------------------------------------
require_once ('../shared/common.php');
require_once REL(__FILE__, '../functions/inputFuncs.php');
require_once REL(__FILE__, '../functions/utilFuncs.php');
require_once REL(__FILE__, '../model/Settings.php');
require_once REL(__FILE__, '../model/Biblios.php');
require_once REL(__FILE__, '../model/BiblioImages.php');
require_once REL(__FILE__, '../model/Copies.php');
require_once REL(__FILE__, '../model/CopyStatus.php');
require_once REL(__FILE__, '../model/CopiesCustomFields.php');
require_once REL(__FILE__, '../model/BiblioCopyFields.php');
require_once REL(__FILE__, '../classes/Biblio.php');
require_once REL(__FILE__, '../classes/Copy.php');

// ---------------------------------------------------------------------------
// 2. Constants (define once at file level — was inside switch cases before!)
// ---------------------------------------------------------------------------
define('UPLOAD_DIR', '../photos/');

// ---------------------------------------------------------------------------
// 3. Session defaults
// ---------------------------------------------------------------------------
// Use null-coalescing to avoid overwriting with Settings values when user
// already has session data.  Only load from Settings when session key is
// genuinely unset (not just empty/falsy — "0" is valid for site IDs).
if (!isset($_SESSION['show_checkout_mbr'])) {
  $_SESSION['show_checkout_mbr'] = Settings::get('show_checkout_mbr');
}
if (!isset($_SESSION['show_detail_opac'])) {
  $_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');
}
if (!isset($_SESSION['multi_site_func'])) {
  $_SESSION['multi_site_func'] = Settings::get('multi_site_func');
}
if (!isset($_SESSION['show_item_photos'])) {
  $_SESSION['show_item_photos'] = Settings::get('show_item_photos');
}
if (!isset($_SESSION['items_per_page'])) {
  $_SESSION['items_per_page'] = Settings::get('items_per_page');
}

// Current site determination
if (!isset($_SESSION['current_site'])) {
  if (isset($_COOKIE['OpenBiblioSiteID'])) {
    $_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];
  } elseif (($_SESSION['multi_site_func'] ?? 0) > 0) {
    $_SESSION['current_site'] = $_SESSION['multi_site_func'];
  } else {
    $_SESSION['current_site'] = '1';
  }
}

  // ## fetch opts here for general use as needed
  // $opts['lookupAvail'] = in_array('lookup2',$_SESSION);
  // $opts['current_site'] = $_SESSION['current_site'];
  // $opts['showBiblioPhotos'] = $_SESSION['show_item_photos'];
  // $opts['barcdWidth'] = $_SESSION['item_barcode_width'];

  ## --------------------- ##

  #****************************************************************************
    //echo "in catalogSrvr, at start: ";print_r($_POST);echo "<br />\n";
// ---------------------------------------------------------------------------
// 4. Dispatch
// ---------------------------------------------------------------------------
$mode = $_POST['mode'] ?? '';

switch ($mode) {

  // ======================================================================
  case 'setCurrentSite':
    $crntSite = $_POST['siteid'] ?? '';
    $_SESSION['current_site'] = $crntSite;
    echo json_encode('crntSite set to ' . $crntSite);
    break;

  // ======================================================================
  case 'doBibidSearch':
    $bibid = (int) ($_POST['bibid'] ?? 0);
    $bib = new Biblio($bibid);
    echo json_encode($bib->getData());
    break;

  // ======================================================================
  case 'doBarcdSearch':
    $searchBarcd = $_POST['searchBarcd'] ?? '';
    $ptr = new Copies();
    $copy = $ptr->getByBarcode($searchBarcd);
    if ($copy !== null) {
      $bib = new Biblio($copy['bibid']);
      echo json_encode($bib->getData());
    } else {
      echo json_encode(['message' => T('No copy with that barcode')]);
    }
    break;

  // ======================================================================
  case 'doPhraseSearch':
    $criteria = $_POST;
    $theDb = new Biblios();
    $biblioLst = $theDb->getBiblioByPhrase($criteria);

    if (count($biblioLst) > 0) {
      $firstItem = (int) ($_POST['firstItem'] ?? 0);
      $itemsPerPage = (int) ($_SESSION['items_per_page'] ?? 20);
      $total = count($biblioLst);

      if ($itemsPerPage <= $total - $firstItem) {
        $lastItem = $firstItem + $itemsPerPage;
      } else {
        $lastItem = $total;
      }

      // Multi-page record header
      $srchRslt = [];
      $srchRslt[] = json_encode([
        'totalNum'  => $total,
        'firstItem' => $firstItem,
        'lastItem'  => $lastItem,
        'itemsPage' => $itemsPerPage,
      ]);

      $iterCounter = 0;
      foreach ($biblioLst as $bibid) {
        $iterCounter++;
        if ($iterCounter - 1 < $firstItem) {
          continue;
        }
        if ($iterCounter > $lastItem) {
          break;
        }
        $bib = new Biblio($bibid);
        $srchRslt[] = json_encode($bib->getData());
        unset($bib);
      }
      echo json_encode($srchRslt);
    } else {
      echo '[]';
    }
    break;

  // ======================================================================
	// ======================================================================
	// ======================================================================
	// ======================================================================
  case 'getCrntMbrInfo':
    require_once REL(__FILE__, '../functions/info_boxes.php');
    echo currentMbrBox();
    break;

  // ======================================================================
  case 'getMediaDisplayInfo':
    require_once REL(__FILE__, '../model/MaterialFields.php');
    $db = new MaterialFields();
    $media = $db->getDisplayInfo($_GET['howMany'] ?? '');
    echo json_encode($media);
    break;

  // ======================================================================
  case 'getMediaLineCnt':
    require_once REL(__FILE__, '../model/MediaTypes.php');
    $db = new MediaTypes();
    $set = $db->getAll('code');
    $media = [];
    foreach ($set as $row) {
      $media[$row['code']] = $row['srch_disp_lines'];
    }
    echo json_encode($media);
    break;

  // ======================================================================
  case 'addToCart':
    require_once REL(__FILE__, '../model/Cart.php');
    $name = $_POST['name'] ?? 'bibid';
    $cart = getCart($name);
    if (isset($_POST['id'])) {
      foreach ($_POST['id'] as $id) {
        $exists = $cart->contains($id);
        if (!$exists) {
          $cart->add($id);
        }
      }
    }
    break;

  // ======================================================================
  case 'delToCart':
    require_once REL(__FILE__, '../model/Cart.php');
    $name = $_POST['name'] ?? 'bibid';
    $cart = getCart($name);
    if (isset($_POST['id'])) {
      foreach ($_POST['id'] as $id) {
        $exists = $cart->contains($id);
        if ($exists) {
          $cart->del($id);
        }
      }
    }
    break;

  // ======================================================================
  case 'getNewBarcd':
    $copies = new Copies();
    $temp = ['barcdNmbr' => $copies->getNewBarCode($_SESSION['item_barcode_width'])];
    echo json_encode($temp);
    break;

  // ======================================================================
  case 'chkBarcdForDupe':
    $copies = new Copies();
    if ($copies->isDuplicateBarcd($_POST['barcode_nmbr'] ?? '', $_POST['copyid'] ?? '')) {
      echo 'Barcode ' . ($_POST['barcode_nmbr'] ?? '') . ': ' . T('Barcode number already in use.');
    }
    break;

  // ======================================================================
  case 'getBiblioFields':
    require_once REL(__FILE__, '../model/MaterialFields.php');
    $db = new Biblios();
    $db->getBiblioFields();
    break;

  // ======================================================================
  case 'getCopyInfo':
    $tab    = strtolower((string) ($_POST['tab'] ?? ''));
    $isOpac = ($tab === 'opac');
    $bibid  = (int) ($_POST['bibid'] ?? 0);

    $bib     = new Biblio($bibid);
    $cpyList = $bib->fetch_copyList();
    $cpys    = [];

    foreach ($cpyList as $cid) {
      $cpy  = new Copy($cid);
      $data = $cpy->getData();

      if ($isOpac) {
        // Strip member-identifying fields for public OPAC view
        foreach (['ckoutMbr', 'mbrId', 'mbrid', 'mbrName', 'first_name', 'last_name'] as $k) {
          unset($data[$k]);
        }
      }

      $cpys[] = $data;
      unset($cpy);
    }

    echo json_encode($cpys);
    break;

  // ======================================================================
  case 'updateBiblio':
    $bibid = (int) ($_POST['bibid'] ?? 0);
    $bib = new Biblio($bibid);

    // Overwrite header
    $hdr = [
      'bibid'         => $bibid,
      'material_cd'   => $_POST['materialCd'] ?? '',
      'collection_cd' => $_POST['collectionCd'] ?? '',
      'opac_flg'      => $_POST['opacFlg'] ?? 'N',
    ];
    $msg = $bib->setHdr($hdr);
    if ($msg !== null) {
      die($msg);
    }

    // Overwrite MARC fields
    $marc = [];
    foreach (($_POST['fields'] ?? []) as $key => $val) {
      $marc[$key] = ['data' => $val['data'], 'codes' => $val['codes']];
    }
    $msg = $bib->setMarc($marc);
    if ($msg !== null) {
      die($msg);
    }

    $msg = $bib->updateDB();
    echo $msg;
    break;

  // ======================================================================
  case 'deleteBiblio_ok':
    $bibid = (int) ($_POST['bibid'] ?? 0);
    $bibs = new Biblios();
    $bibs->deleteOne_new($bibid);
    echo T('Delete completed');
    break;

  // ======================================================================
  case 'deleteMultiBiblios':
    $bibList = $_POST['bibList'] ?? [];
    $bibs = new Biblios();
    foreach ($bibList as $bibid) {
      $bibs->deleteOne_new($bibid);
    }
    echo T('Delete completed');
    break;

  // ======================================================================
  case 'newCopy':
    $barcode   = $_POST['barcode_nmbr'] ?? '';
    $copyBibid = (int) ($_POST['bibid'] ?? 0);
    $copies    = new Copies();

    if ($copies->isDuplicateBarcd($barcode, '')) {
      echo 'Barcode ' . $barcode . ': ' . T('Barcode number already in use.');
      break;
    }
    $db = new Copies();
    echo $db->insertCopy($copyBibid, '');
    break;

  // ======================================================================
  case 'updateCopy':
    $db = new Copies();
    echo $db->updateCopy(
      (int) ($_POST['bibid'] ?? 0),
      (string) ($_POST['copyid'] ?? '')
    );
    break;

  // ======================================================================
  case 'getBibsFrmCopies':
    $db = new Copies();
    $rslt = $db->getBibsForCpys($_GET['cpyList'] ?? '');
    echo json_encode($rslt);
    break;

  // ======================================================================
  case 'deleteCopy':
    $db = new Copies();
    echo $db->deleteCopy($_POST['copyid'] ?? '');
    break;

  // ======================================================================
  case 'deleteMultiCopies':
    $cpyList = $_POST['cpyList'] ?? [];
    $db = new Copies();
    foreach ($cpyList as $copyid) {
      echo $db->deleteCopy($copyid);
    }
    break;

  //// ====================================////
  // ======================================================================
  case 'getPhoto':
    $ptr = new BiblioImages();
    $set = $ptr->getByBibid((int) ($_POST['bibid'] ?? 0));
    $imgs = [];
    foreach ($set as $row) {
      $imgs[] = $row;
    }
    echo json_encode($imgs !== [] ? $imgs : '');
    break;

case 'updatePhoto': // this is currently not working but manually deleting, and adding works --F.Tumulak
    $ptr = new BiblioImages;
    $rslt = $ptr->deleteByBibid((int) ($_POST['bibid'] ?? 0));
    echo json_encode($rslt);

    $filename = basename(str_replace(['../', './'], '', $_POST['url']));
    $file = UPLOAD_DIR . $filename;

    // Step 2: Sanitize and decode the base64 image string
    $img = $_POST['img'] ?? '';
    if (empty($img)) {
        echo json_encode(['error' => 'Image data is empty']);
        break;
    }

    $imgFmt = (strtolower(substr($file, -3)) === 'png') ? 'png' : 'jpeg';

    // Remove the data URI prefix and decode base64
    $img = str_replace('data:image/'.$imgFmt.';base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);

    // Step 3: Save the new image
    $success = file_put_contents($file, $data);
    if ($success === false) {
        echo json_encode([
            'error' => "Unable to save file to $file",
            'postData' => $_POST
        ]);
        break;
    }

    // Step 4: Add image record to the database
    $err = $ptr->appendLink_e((int) ($_POST['bibid'] ?? 0), $_POST['caption'], $data, $file);
    if ($err) {
        echo json_encode(['error' => $err]);
        break;
    }

    // Step 5: Return all image records for that bibid
    $set = $ptr->getByBibid((int) ($_POST['bibid'] ?? 0));
    $imgs = [];
    foreach ($set as $row) {
        $imgs[] = $row;
    }
    echo json_encode($imgs);
    break;



  case 'addNewPhoto':

    $ptr = new BiblioImages;
    $rslt = $ptr->deleteByBibid((int) ($_POST['bibid'] ?? 0));
    echo json_encode($rslt);

    $filename = basename(str_replace(['../', './'], '', $_POST['url']));
    $file = UPLOAD_DIR . $filename;

    // 🔐 Check if file exists before attempting to delete
    if (file_exists($file)) {
        unlink($file);
    } else {
        // Optional: Log or silently ignore
        // error_log("File not found: $file");
    }


    $img = $_POST['img'];
    if (substr($file, -3,3) == 'png')
      $imgFmt = 'png';
     else
      $imgFmt = 'jpeg';
    $img = str_replace('data:image/'.$imgFmt.';base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $success = file_put_contents($file, $data);
    if ($success) {
        $ptr = new BiblioImages;
      $err = $ptr->appendLink_e((int) ($_POST['bibid'] ?? 0), $_POST['caption'], $data, $file);
      if(isset($err)) {
        print_r($err);
        break;
      }
        $set = $ptr->getByBibid((int) ($_POST['bibid'] ?? 0));
              foreach ($set as $row) {
        $imgs[] = $row;
      }
      echo json_encode($imgs);
    } else {
      echo "Unable to save the file < $file >.\n";
      print_r($_POST);
    }
    break;

  case 'addNewRemotePhoto':
    $ptr = new BiblioImages;
    $caption = $_POST['caption'] ? $_POST['caption'] : 'Cover';
    $err = $ptr->insert_el(['bibid' => (int) ($_POST['bibid'] ?? 0), 'caption' => $caption, 'url' => $_POST['url'], 'imgurl' => $_POST['url']]);
      if(isset($err)) {
        print_r($err);
        break;
      }
      $set = $ptr->getByBibid((int) ($_POST['bibid'] ?? 0));
      foreach ($set as $row) {
        $imgs[] = $row;
      }
      echo json_encode($imgs);
    break;

  case 'deletePhoto':
      $ptr = new BiblioImages;
      $rslt = $ptr->deleteByBibid((int) ($_POST['bibid'] ?? 0));
      echo json_encode($rslt);

      $filename = basename(str_replace(['../', './'], '', $_POST['url']));
      $file = UPLOAD_DIR . $filename;

      // 🔐 Check if file exists before attempting to delete
      if (file_exists($file)) {
          unlink($file);
      } else {
          // Optional: Log or silently ignore
          // error_log("File not found: $file");
      }
      break;

  //// ====================================////
  default:
    echo "<h5>Invalid mode: " . H((string) $_POST['mode']) . "</h5>";
  }