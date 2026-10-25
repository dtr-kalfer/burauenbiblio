<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  ##-----------
  function getErrInfo($errCode) {
    $msg = '';
    switch ($errCode) {
      case 10007:
        $msg = "Host Timeout";
        break;
      default:
        $msg = "unknown errCode: $errCode";
        break;
    }
    return $msg;
  }

  ##-----------
  function verifyLCCN ($lookupVal) {
      ## remove "-" and fill with "0" to make 8 char long
      $pos = strPos($lookupVal, "-");
      if ($pos > 0) {
        $lccnLeft = subStr($lookupVal,0,$pos);
        $lccnRight = subStr($lookupVal,$pos+1,6);
        $lccnRight = str_pad($lccnRight,6,"0",STR_PAD_LEFT);
        $lookupVal = $lccnLeft . $lccnRight;
      }
      return $lookupVal;
  }

  ##-----------
  function verifyISBN($lookupVal,$keepDashes) {
      global $postVars;

    ## remove any "-" char user may have entered
    if (($postVars['keepDashes'] ?? '')=='n') $lookupVal = str_replace("-", "", $lookupVal);
    ## remove any space char user may have entered
    $lookupVal = str_replace(" ", "", $lookupVal);

    return $lookupVal;
  }

  ##-----------
  function doOneHost($host, $hits, $id) {
    global $errMsg, $xml_parser;
    $rslt = array();

    if (!isset($hits[$host]) || $hits[$host] == 0) {
      $rslt['1'] = array('err'=>$errMsg[$host] ?? 'No hits or host error');
      return $rslt;
    }

    for ($hit = 1; $hit<=$hits[$host]; $hit++) {
      $rcdFmt = 'USMARC';
      if (function_exists('yaz_record') && !empty($id[$host])) {
        $rcdFmt = yaz_record($id[$host], $hit, "syntax");
      }

      if (strtoupper($rcdFmt) != 'XML') {
        $ar = yaz_record($id[$host], $hit, "array");
        if (!empty($ar)) {
          $rslt[$hit] = get_marc_fields_from_x3950($ar, true, $hit, $host);
        }
      }
      else {
        $data = yaz_record($id[$host], $hit, "xml");
        xml_parse_into_struct($xml_parser, $data, $hostRecords);
        list($num_records, $parsed_rslt) = get_marc_fields_from_xml($hostRecords);
        if (!empty($parsed_rslt)) {
          $rslt[$hit] = $parsed_rslt[0] ?? $parsed_rslt;
        }
      }
    }
    return $rslt;
  }

  ##-----------
  function get_marc_fields_from_xml($xml) {
    $marc = array();
    $respVersion = '';
    $recordposition = 0;
    $subcount = 0;
    $total_hits = 0;
    $diagMsg = '';
    $wantMsg = false;

    if (!is_array($xml)) {
      return array(0, array());
    }

    foreach($xml AS $record)   {
      switch($record['tag'])     {
      case 'ZS:VERSION':
        $respVersion = $record['value'] ?? '';
        break;
      case 'ZS:NUMBEROFRECORDS':
        $total_hits = $record['value'] ?? 0;
        break;
      case 'ZS:DIAGNOSTICS':
        if (($record['type'] ?? '') == 'open') {
          $wantMsg = true;
      }
        break;
      case 'MESSAGE':
        if ($wantMsg)  {
      $marc[$recordposition]['diagMsg'] = $record['value'] ?? '';
          $wantMsg = false;
      }
        break;
       case 'CONTROLFIELD':
        if (isset($record['attributes']['TAG'])) {
          $marc[$recordposition][$record['attributes']['TAG']] = trim($record['value'] ?? '');
        }
        break;
      case 'DATAFIELD':
        if(isset($record['attributes']))       {
          $datafield = $record['attributes']['TAG'] ?? '';
        }
        break;
      case 'SUBFIELD':
        $code = $record['attributes']['CODE'] ?? '';
        $value = $record['value'] ?? '';
        $indicie = ($datafield ?? '') . $code;
        $extratrim = '';
        switch($indicie) {
        case 'MARC_ISBN':
          if (substr($value,0,3) == '978')
            $value = substr($value, 0, 13);
      else
            $value = substr($value, 0, 10);
          break;
        case 'MARC_TITLE':
        case 'MARC_PUBLICATION_PLACE':
          $extratrim = ':/';
          break;
        case 'MARC_SUBTITLE':
          $extratrim = '/';
          break;
        case 'MARC_PUBLISHER':
          $extratrim = ',';
          break;
        case 'MARC_PAGES':
          $value = (int)($value);
          break;
        }
        if($indicie != 'MARC_SUBJECT')       {
          if(isset($marc[$recordposition][$indicie]) && !empty($marc[$recordposition][$indicie])) {
            $marc[$recordposition][$indicie] .= ', ' . trim($value, ' '.$extratrim);
          } else {
            $marc[$recordposition][$indicie] = trim($value, ' '.$extratrim);
          }
        } else {
          if($subcount == 0) {
            $marc[$recordposition][$indicie] = trim($value, ' '. $extratrim);
            $subcount++;
          } else {
            $marc[$recordposition][$indicie.$subcount] = trim($value, ' ' . $extratrim);
            $subcount++;
          }
        }
        break;
      case 'ZS:RECORDPOSITION':
        $recordposition++;
        $subcount = 0;
        break;
      }
    }

    if (($recordposition == 0) && ($total_hits > 0))
      $recordposition = 1;

    return array($recordposition, $marc);
  }

  ##-----------
  function get_marc_fields_from_x3950($ar, $postit, $hit, $host) {
    global $my_callNmbrType;
    $rslt = array();
    $fld100a = false;
    $theTag = '';
    $subFlds = array();

    if (!is_array($ar)) {
      return $rslt;
    }

    foreach ($ar as $row) {
      if (!isset($row[0]) || !isset($row[1])) continue;
      $tagpath = $row[0];
      $data = $row[1];

      if (preg_match("/^\(3,([^)]*)\)\(3,([^)]*)\)$/", $tagpath, $res)) {
        $theTag = "$res[1]";
        $subFlds = array();
      }
      elseif (preg_match("/^\(3,([^)]*)\)\(3,([^)]*)\)\(3,([^)]*)\)$/", $tagpath, $res)) {
      $theTag = trim($res[1]);
      $subfield = trim($res[3]);

      $subFlds[$subfield] = (string)$data;

      $data = trim(
          htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8')
      );

      $fldId = $theTag . $subfield;

        if ($fldId == '100a')
          $fld100a = true;
        elseif ($fldId == '700a' && !$fld100a)
          $fldId = '100a';

        if (!isset($rslt[$fldId])) {
          $rslt[$fldId] = '';
        } else {
          $rslt[$fldId] .= '; ';
        }

        switch ($theTag) {
          case '538':
            if ($res[3] == 'a') {
              $rslt['520a'] = ($rslt['520a'] ?? '') . $data;
            }
            break;

        case '650':
            if ($res[3] == 'a') {
                if (!isset($subjectCnt)) {
                    $subjectCnt = 0;
                }

                $subjectKey = '650a' . $subjectCnt;
                $rslt[$subjectKey] = ($rslt[$subjectKey] ?? '') . $data;

                $subjectCnt++;
            } else {
                if (!is_numeric($res[3]) && isset($subjectCnt) && $subjectCnt > 0) {
                    $subjectKey = '650a' . ($subjectCnt - 1);
                    $rslt[$subjectKey] = ($rslt[$subjectKey] ?? '') . ', ' . $data;
                }
            }
            break;

          default:
            $rslt[$fldId] .= $data;
            break;
        }
      }
    }

    // Fallback for call numbers if 099a or 050a are not standard in this target
    if (empty($rslt['099a'])) {
      if (!empty($rslt['090a'])) {
        $rslt['099a'] = $rslt['090a'] . (!empty($rslt['090b']) ? ' ' . $rslt['090b'] : '');
      } elseif (!empty($rslt['050a'])) {
        $rslt['099a'] = $rslt['050a'] . (!empty($rslt['050b']) ? ' ' . $rslt['050b'] : '');
      } elseif (!empty($rslt['852h'])) {
        $rslt['099a'] = $rslt['852h'];
      }
    }

    return $rslt;
  }

  ## ---------------------------------- ##
  function postNewBiblio() {
    require_once(REL(__FILE__, "../model/Biblios.php"));
    require_once(REL(__FILE__, "../classes/Marc.php"));
      include(REL(__FILE__,'../catalog/biblioChange.php'));

    $input = $_POST;
    unset($_POST);
    $rec = [];
    foreach ($input as $k=>$v) {
      if (($k != 'fields') && (substr($k,0,4) != 'onln')) {
        $_POST[$k] = $v;
      } else if ($k == 'fields') {
        foreach ($v as $fld) {
          $tag = $fld['tag'].'$'.$fld['subfield_cd'];
          $rec[$tag]['data'] = $fld['data'];
          $rec[$tag]['codes'] = 'subfieldid='.$fld['subfieldid'].'&fieldid='.$fld['fieldid'];
        }
      }
    }
    $_POST['fields'] = $rec;
      $nav = "newconfirm";
        $msg = PostBiblioChange($nav);
      echo $msg;
  }
