<?php
        //echo "postVars: ";print_r($postVars);echo"<br />\n";
        require_once(REL(__FILE__, '../catalog/olSrchVals.php'));

    #### perform the search ####
    $numHosts = $postVars['numHosts'];
    $temp = $postVars['opts'];
    $opts = $temp[0];
    $maxHits = (int)($opts['maxHits'] ?? 25);
    if (function_exists('yaz_connect')){
      require_once (REL(__FILE__, '../catalog/olYazSrch.php'));
    } else {
      require_once (REL(__FILE__, '../catalog/olSruSrch.php'));
    }

    #### process the results ####
    $initialCond = false;
    $rcd['ttlHits'] = $ttlHits;
    $num_Records = '';

    if ($ttlHits == 0) {
      ## TOO FEW
      /* error encountered */
      $collectedErrors = array();
      for ($i=0; $i<$numHosts; $i++) {
        if (!empty($errMsg[$i])) {
          $hName = $postVars['hosts'][$i]['name'] ?? "Host #$i";
          $collectedErrors[] = "$hName: " . $errMsg[$i];
        }
      }

      if (!empty($collectedErrors)) {
        $rcd['msg'] = implode("<br />", $collectedErrors);
      } else {
        $rcd['msg'] = T("Nothing Found");
      }

      $srch1['byName'] = $srchByName;
      $srch1['lookupVal'] = $lookupVal;
      $rcd['srch1'] = json_encode($srch1);
      if (isset($srchByName2)) {
        $srch2['byName'] = $srchByName2;
        $srch2['lookupVal'] = $lookupVal2;
        $rcd['srch2'] = json_encode($srch2);
      } else {
        $srch2['byName'] = 0;
        $srch2['lookupVal'] = '';
        $rcd['srch2'] = json_encode($srch2);
      }
      echo json_encode($rcd);
      exit;
    }

    else if ($ttlHits > $maxHits) {
            $rcd['msg'] = T("lookup_tooManyHits");
            $rcd['msg2'] = T("lookup_refineSearch");
            echo json_encode($rcd);
            exit;
    }

    else if ($ttlHits > 0) {
      ## GOOD COUNT
      if ($numHosts > 0) {
        $postit = true;
        $rslt = array();
        //$_POST['postVars'] = $postVars; // for debugging
        $xml_parser = xml_parser_create();

        if (function_exists('yaz_connect')) {
          for ($h=0; $h<$numHosts; $h++) {
            $rslt[$h] = doOneHost($h, $hits, $id);
          }
        }

        else {
          ### no PHP_YAZ processor available
          for ($h=0; $h<$numHosts; $h++) {
            if (!empty($hits[$h])) {
              xml_parse_into_struct($xml_parser, $hits[$h], $hostRecords[$h]);
              list($num_Records, $rslt[$h]) = get_marc_fields_from_xml($hostRecords[$h]);
              $msg = $rslt[$h][0]['diagMsg'] ?? '';
              if (($num_Records == 0) && (!empty($msg))) {
                echo "Host Diagnostic Response: $msg<br />\n";
                echo "----<br />Details...<br />\n";
                echo $qry."<br />\n";
                print_r($rslt);echo "<br />\n";
                exit;
              }
              $ttlHits += $num_Records;
            }
          }

        }
        xml_parser_free($xml_parser);
        $_POST['data'] = $rslt;
        $_POST['ttlHits'] = $ttlHits;
        $_POST['numHosts'] = $numHosts;
      }
        $json = json_encode($_POST);

        if ($json === false) {
            error_log(
                'BurauenBiblio JSON error: ' .
                json_last_error_msg()
            );

            header('Content-Type: text/plain; charset=UTF-8');

            echo 'JSON encoding failed: ';
            echo json_last_error_msg();

            exit;
        }

        header('Content-Type: application/json; charset=UTF-8');

        echo $json;
        exit;
    }
