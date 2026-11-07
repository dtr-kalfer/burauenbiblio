<?php
  require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, '../model/Hosts.php'));
	require_once(REL(__FILE__, '../model/Opts.php'));
	require_once(REL(__FILE__, '../catalog/olSrvrFuncs.php'));	## general support functions
	$optr = new Opts;
	$opts = $optr->getAll();
	$postVars['opts'] = $opts->fetchAll();

	if (!empty($_POST['srchHost'])) {
		$useHosts = array();
		foreach ($_POST['srchHost'] as $val ) {
		  	$useHosts[] = $val;
		}
	}

	$hptr = new Hosts;
	$hosts = array();
	$hSet = $hptr->getMatches(array('active'=>'y'), 'seq');
	foreach ($hSet as $row) {
		if (!empty($useHosts)) { // have user selection available
    	  	if (in_array($row['id'], $useHosts)) {
    			$hosts[] = $row;
    		}
    	} else {   // no input from user, use all in db
    		$hosts[] = $row;
    	}
    }
	$postVars['hosts'] = $hosts;
	$postVars['numHosts'] = count($hosts);
	$postVars['session'] = $_SESSION;
	// echo "in onlineServer, above switch() <br />\n";
	// print_r($postVars);echo "<br />\n";

    ## main body of code
    switch ($_POST['mode']){
    	case 'getBiblioFields':
    	  require_once(REL(__FILE__,"../catalog/biblioFields.php"));
    	  break;

    	case 'doInsertBiblio':
    	  postNewBiblio();
    	  break;

    	case 'getHosts':
    		echo json_encode($postVars['hosts']);
    		break;

    	case 'getOpts':
    		echo json_encode($postVars);
    		break;

    	case 'getCutter':
    		if ($postVars['cutterType'] == 'LoC') {
    			require_once('../catalog/olCutterLoc.php');
    		} elseif ($postVars['cutterType'] == 'CS3') {
    			require_once('../catalog/olCutterCs3.php');
    		} else {
    			echo "Invalid cutter type selection - ".$postVars['cutterType']." <br />";
    		}

    		$temp['cutter'] = getCutter($_POST['author']);
    		echo json_encode($temp);
    		break;

    	case 'search':
    		require_once(REL(__FILE__,'../catalog/olSearch.php')); ## will respond directly, depending on what is received
    		break;

    	case 'abandon':
    		for ($n=0; $n<$postVars['numHosts']; $n++) {
    			yaz_close($id[$n]);
    		}
    		break;

    	default:
    	   echo T("invalid mode").": ".$_POST['mode']." <br />";
    	   break;
    }