<?php
    $hitNmbr = -1;
    $marcFlds = array();
    $subFlds = array();

    #### pagination search limits
    $startAt = 1;
    $nmbr = isset($maxHits) ? (int)$maxHits : 25;

    $id = array();
    $hits = array();
    $errMsg = array();

    for ($ptr=0; $ptr<$postVars['numHosts']; $ptr++) {
      $aHost = $postVars['hosts'][$ptr]['host'];
      $aPort = $postVars['hosts'][$ptr]['port'];
      $aUrl  = $aHost.':'.$aPort;

      $yazOpts = array();
      if (!empty($postVars['hosts'][$ptr]['user'])) {
        $yazOpts['user'] = $postVars['hosts'][$ptr]['user'];
      }
      if (!empty($postVars['hosts'][$ptr]['pw'])) {
        $yazOpts['password'] = $postVars['hosts'][$ptr]['pw'];
      }
      $aSvc = $postVars['hosts'][$ptr]['service'] ?? 'Z3950';

      if ($aSvc == 'Z3950') {
        $yazOpts['sru'] = '';
        $srchType = 'rpn';
        $query = $zQuery;
      }
      else {  // SRU/SRW
        $yazOpts['sru'] = 'get';
        $srchType = 'cql';
        $query = $sQuery;
      }

      $connOK = @yaz_connect($aUrl, $yazOpts);
      if (!$connOK) {
        $hits[$ptr] = 0;
        $errMsg[$ptr] = "Connection failed to " . ($postVars['hosts'][$ptr]['name'] ?? $aUrl);
        $id[$ptr] = false;
        continue;
      }

      $id[$ptr] = $connOK;
      @yaz_database($id[$ptr], $postVars['hosts'][$ptr]['db']);
      $syntax = !empty($postVars['hosts'][$ptr]['syntax']) ? $postVars['hosts'][$ptr]['syntax'] : 'usmarc';
      @yaz_syntax($id[$ptr], $syntax);
      @yaz_element($id[$ptr], "F");
      @yaz_range($id[$ptr], (int)$startAt, (int)$nmbr);

      if (!@yaz_search($id[$ptr], $srchType, $query)) {
        $errMsg[$ptr] = "Failed to submit search query to " . ($postVars['hosts'][$ptr]['name'] ?? $aUrl);
        $hits[$ptr] = 0;
      }
    }

    #### wait for hosts with a reasonable timeout
    $timeout = !empty($postVars['opts'][0]['timeout']) ? (int)$postVars['opts'][0]['timeout'] : 10;
    if ($timeout <= 0 || $timeout > 15) {
      $timeout = 10; // Cap at 10s to prevent browser AJAX hanging
    }
    $waitOpts = array('timeout' => $timeout);
  
    @yaz_wait($waitOpts);

    $ttlHits = 0;
    for ($i=0; $i<$numHosts; $i++) {
      if (empty($id[$i])) {
        $hits[$i] = 0;
        if (empty($errMsg[$i])) {
          $errMsg[$i] = "Host not reachable";
        }
        continue;
      }

      $error = yaz_error($id[$i]);
      $errno = yaz_errno($id[$i]);
      if (!empty($error) || $errno != 0) {
        $hits[$i] = 0;
        $errText = yaz_addinfo($id[$i]);
        if (empty($errText)) {
          $errText = getErrInfo($errno);
        }
        if (empty($errText)) {
          $errText = $error;
        }
        $errMsg[$i] = "(Error " . $errno . ") " . $errText;
      } else {
        $hostHits = (int)@yaz_hits($id[$i]);
        $hits[$i] = $hostHits;
        $errMsg[$i] = '';
      }
      $ttlHits += $hits[$i];
    }
