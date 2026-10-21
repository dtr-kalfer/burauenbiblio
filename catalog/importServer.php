<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../model/Collections.php"));
  require_once(REL(__FILE__, "../model/MediaTypes.php"));
  require_once(REL(__FILE__, "../model/MarcBlocks.php"));
  require_once(REL(__FILE__, "../model/MarcTags.php"));
  require_once(REL(__FILE__, "../model/MarcSubfields.php"));
  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/Biblios.php"));
  require_once(REL(__FILE__, "../model/Cart.php"));
  require_once(REL(__FILE__, "../classes/Marc.php"));

  # Big uploads take a while
  set_time_limit(300);

//  $recordTerminator="\n";
 $recordTerminator = "\r\n";

// $fileContent = file_get_contents($fn);
// if (strpos($fileContent, "\r\n") !== false) {
    // $recordTerminator = "\r\n";
// } else {
    // $recordTerminator = "\n";
// }

  ## ---------------------------------------------------------------------- ##

function doPostNewBiblio($rcrd) {
  $_POST = $rcrd;
    require_once(REL(__FILE__,'../catalog/biblioChange.php'));
    $rtn = PostBiblioChange("newconfirm");
    $rslt = json_decode($rtn);
    return $rslt->bibid;
}

## main body of code
//echo "params: ";print_r($_POST);echo "<br />\n";
switch ($_POST['mode']){
    #-.-.-.-.-.-.-.-.-.-.-.-.-
    case 'isDupBarCd':
      $cpys = new Copies;
      $rslt = $cpys->isDuplicateBarcd($_GET['barCd']);
      echo $rslt;
      break;

    #-.-.-.-.-.-.-.-.-.-.-.-.-
    case 'getCollections':
    $cols = new Collections;
    $collections = $cols->getSelect();
    echo json_encode($collections);
      break;
  case 'getMediaTypes':
    $meds = new MediaTypes;
    $medTypes = $meds->getSelect();
    echo json_encode($medTypes);
      break;
  case 'getMarcDesc':
      $tag = explode('$', $_GET['code']);
    $ptr = new MarcSubfields;
    $params = array('tag' =>$tag[0], 'subfield_cd' =>$tag[1] );
      $vals = array();
    $rslt = $ptr->getMatches($params, 'subfield_cd');
    while ($row = $rslt->fetch_assoc()) {
        $vals[] = $row;
    }
    $val = $vals[0]['description'];
      echo $val;
      break;

    #-.-.-.-.-.-.-.-.-.-.-.-.-
  case 'processMarcFile':
    $fn = $_FILES['imptSrce']['tmp_name'];
    if (is_uploaded_file($fn)) {
      $f = fopen($fn, 'rb');
      if (!$f) {
        echo '<p class="error">'.T("Unable to open uploaded file").'</p>';
        break;
      }

      $biblios = new Biblios();
      $p = new MarcParser();
      $cart = new Cart('bibid');
      $nfnd = 0;
      $nimp = 0;
      $nerrs = 0;

      $opac_flg = (isset($_POST['opacFlg']) && $_POST['opacFlg'] == 'Y') ? 'Y' : 'N';
      $material_cd = isset($_POST['materialCd']) ? $_POST['materialCd'] : '';
      $collection_cd = isset($_POST['collectionCd']) ? $_POST['collectionCd'] : '';

      if ($_POST["test"] == "true") {
        echo '<p class="info">'.T("Test mode was selected - input file was error checked, nothing has been imported.").'</p>';
      }

      while (!feof($f)) {
        $buf = fread($f, 8192);
        if ($buf === false || strlen($buf) === 0) {
          break;
        }
        $rslt = $p->parse($buf);
        if (is_a($rslt, 'MarcParseError')) {
          $nerrs++;
          echo '<p class="error">'.T("MARC parse error").': '.H($rslt->toStr()).'</p>';
          continue;
        }
        foreach ($p->records as $rec) {
          $nfnd++;
          if ($_POST["test"] == "true") {
            if ($_POST["verbose"] == "true") {
              echo '<p>'.T("Record").' #'.$nfnd.'</p>';
              echo '<pre>'.H($rec->getMnem()).'</pre>';
            }
          } else {
            $biblio = array(
              'last_change_userid' => $_SESSION["userid"],
              'material_cd' => $material_cd,
              'collection_cd' => $collection_cd,
              'opac_flg' => $opac_flg,
              'marc' => $rec,
            );
            list($bibid, $errors) = $biblios->insert($biblio);
            if ($errors && $errors !== 'Success') {
              $nerrs++;
              $errStrs = array_map(function($e) { return is_object($e) && method_exists($e, 'toStr') ? $e->toStr() : (string)$e; }, is_array($errors) ? $errors : [$errors]);
              echo '<p class="error">'.T("Record").' #'.$nfnd.': '.implode(', ', $errStrs).'</p>';
            } else {
              $nimp++;
            }
          }
        }
        $p->records = array();
      }

      // Flush any remaining records in the parser buffer
      $remaining = $p->eof();
      if (is_a($remaining, 'MarcParseError')) {
        $nerrs++;
        echo '<p class="error">'.T("Trailing data error").': '.H($remaining->toStr()).'</p>';
      } else if ($remaining > 0) {
        // Any records from eof() are already in $p->records
        foreach ($p->records as $rec) {
          $nfnd++;
          if ($_POST["test"] != "true") {
            $biblio = array(
              'last_change_userid' => $_SESSION["userid"],
              'material_cd' => $material_cd,
              'collection_cd' => $collection_cd,
              'opac_flg' => $opac_flg,
              'marc' => $rec,
            );
            list($bibid, $errors) = $biblios->insert($biblio);
            if ($errors && $errors !== 'Success') {
              $nerrs++;
              $errStrs = array_map(function($e) { return is_object($e) && method_exists($e, 'toStr') ? $e->toStr() : (string)$e; }, is_array($errors) ? $errors : [$errors]);
              echo '<p class="error">'.T("Record").' #'.$nfnd.': '.implode(', ', $errStrs).'</p>';
            } else {
              $nimp++;
            }
          }
        }
      }

      fclose($f);
      echo '<p>'.$nfnd.' '.T("records found").'</p>';
      echo '<p>'.$nimp.' '.T("records imported").'</p>';
      if ($nerrs > 0) {
        echo '<p>'.$nerrs.' '.T("errors encountered").'</p>';
      }
      if ($_POST["test"] != "true" && $nimp > 0) {
        echo '<p>'.T("Records imported to database").'</p>';
      }
    } else {
      echo '<p class="error">'.T("Error - file did not load successfully!").'</p>';
    }
    break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
  case 'fetchCsvFile':
    $fn = $_FILES['imptSrce']['tmp_name'];
    if (is_uploaded_file($fn)) {
      $rows =  explode($recordTerminator, file_get_contents($fn));
      //check the last record if there is content after the delimiter
      $row = array_pop($rows);
      if (trim($row) != "") {
        array_push($rows, $row);
      }
      echo json_encode($rows);
    } else {
      echo  T("error - file did not load successfully!!")."<br />";
    }
    break;

  case 'postCsvData':
      $theDb = new Copies;
    $cpys = new Copies;

    $rec = $_POST['record'];
    $barCd = $rec['barcode_nmbr'];
    $cpyAction = $rec['copy_action'];
    if ($barCd == 'autogen') {
      $barCd = $cpys->getNewBarCode($_SESSION['item_barcode_width']);
      $rec['barcode_nmbr'] = $barCd;
    }

    $bibid = doPostNewBiblio($rec);
    $text = "Biblio #$bibid posted successfully.";
    if (($cpyAction >= 1) && (isset($rec['barcode_nmbr']))) {
      if ($theDb->insertCopy($bibid,NULL) == '!!success!!') {
        $text .= "<br />\n".T("Copy successfully created.")." ".T("Barcode")." = ".$barCd.". ";
      } else {
        $text .= "<br />\n".T("Error creating New Copy")." ".T("Barcode")." = ".$barCd.". ";
      }
    } else {
    }
    echo $text;
    break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
  default:
      echo T("invalid mode") . " : " . $_POST['mode'] . "<br />";
    break;
}