<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
		//echo "using YAZ module <br />\n";

		$hitNmbr = -1;
		$marcFlds = array();
		$subFlds = array();

		#### pagination search limits - may be user sperified in future;
		$startAt = 1;
		//$nmbr = 50; //$postVars['maxHits'];
		//$nmbr = $maxHits; // extracted from onLine Opts db table
		//echo "return results from #$startAt to #". ($startAt+$nmbr-1) ."<br />";

		//echo "using $postVars['numHosts'] host(s)<br />";
		for ($ptr=0; $ptr<$postVars['numHosts']; $ptr++) {
			$aHost = $postVars['hosts'][$ptr]['host'];
			$aPort = $postVars['hosts'][$ptr]['port'];
			$aUrl  = $aHost.':'.$aPort;
			
			$yazOpts['user'] = $postVars['hosts'][$ptr]['user'];
			$yazOpts['password'] = $postVars['hosts'][$ptr]['pw'];
			$aSvc	 = $postVars['hosts'][$ptr]['service'];

			if ($aSvc == 'Z3950') {
				$yazOpts['sru'] = ''; // not used for z39.50
				$srchType = 'rpn';
				$query = $zQuery;
			}
			else {	//SRU/SRW
				$yazOpts['sru'] = 'get'; // legal values are get,post,soap
				$srchType = 'cql';
				$query = $sQuery;
			}
            //echo "query=$query <br />\n";

			$connOK = yaz_connect($aUrl, $yazOpts );
			if (! $connOK) {
				echo 'yaz setup not successful! <br />';
				trigger_error(T("lookup_yaz_setup_failed").$postVars['hosts'][$ptr]['name']."<br />", E_USER_ERROR);
			} else {
                //echo 'yaz setup successful! <br />';
				$id[$ptr] = $connOK;
				yaz_database($id[$ptr], $postVars['hosts'][$ptr]['db']);
				yaz_syntax($id[$ptr], $postVars['hosts'][$ptr]['syntax']);
				yaz_element($id[$ptr], "F");
				//if (! yaz_range($id[$ptr], (int)$startAt, (int)$nmbr) ) {
				//	echo "Host $aUrl does not appear to accept limits on number of hits.<br />";
				//}
				yaz_range($id[$ptr], (int)$startAt, (int)$nmbr);
                //echo "sending: $query <br />";
				if (! yaz_search($id[$ptr], $srchType, $query)) 
					trigger_error(T("lookup_badQuery")."<br />", E_USER_NOTICE);
			}
		}

		#### now wait for ALL hosts to return ALL results
//		$waitOpts = array("timeout"=>$postVars['timeout']);
$waitOpts = array('timeout'=>60);
        //echo "<br /> waiting {$waitOpts['timeout']} seconds for responses. <br />";
		yaz_wait($waitOpts);

		$ttlHits = 0;
		//echo "processing rslts for $numHosts host(s)<br />";
		for ($i=0; $i<$numHosts; $i++) {
			## did we make it?
			$error = yaz_error($id[$i]);
			if (!empty($error)) {
				//echo "error response from host.<br />";
				$hits[$i] = 0;
				$errText = yaz_addinfo($id[$i]);
				if ($errText == '') {
					$errText = getErrInfo(yaz_errno($id[$i]));
				}
				$errMsg[$i] = "(yaz err no. " . yaz_errno($id[$i]) . ') ' . $errText;
			} else {
				//echo "host responded without error.<br />";
				$hits[$i] = yaz_hits($id[$i]);
				$errMsg[$i] = '';
				//echo "Host #$i {$postVars[hosts][$i]['name']} result Count: $hits[$i] <br />";
			}
			$ttlHits += $hits[$i];
		}
		//echo "Total Hits=$ttlHits <br />";
