<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

		function getlabel($f) {
			global $LOC;
			$label = "";
			if ($f['label'] != "") {
				$label = $f['label'];
			} elseif ($f['subfield'] != "") {
				$idx = sprintf("%03d$%s", $f['tag'], $f['subfield']);
				$label = $LOC->getMarc($idx);
			} else {
				$label = $LOC->getMarc($f['tag']);
			}
			return $label;
		}
		function mkinput($fid, $sfid, $data, $f, $n) {
			$ar = array(
				'fieldid' => $fid,
				'subfieldid' => $sfid,
				'data' => $data,
				'tag' => $f['tag'],
				'subfield' => $f['subfield_cd'],
				'label' => getlabel($f),
				'required' => $f['required'],
				'form_type' => $f['form_type'],
				'repeat' => $f['repeatable']
				);
			if (($f['repeatable']) && ($n > 0)) {
				$ar['subfield'] .= $n;
			}
			return $ar;
		}
		function mkFldSet($n, $i, $marcInputFld, $mode) {
		  	if ($mode == 'onlnCol') {
				echo "	<td valign=\"top\" class=\"filterable\"> \n";
				$namePrefix = "onln_$n";
		    	echo "<input type=\"button\" value=\"<--\" id=\"$namePrefix"."_btn\" class=\"accptBtn\" /> \n";
			} else if ($mode == 'editCol') {
				echo "	<td valign=\"top\" > \n";
				$namePrefix = 'fields['.H($n).']';
				echo inputfield('hidden', $namePrefix."[tag]",         H($i['tag']))." \n";
				if ($i['repeat']) {
					echo inputfield('hidden', $namePrefix."[subfield_cd]", H($i['subfield']))." \n";
				} else {
					echo inputfield('hidden', $namePrefix."[subfield_cd]", substr(H($i['subfield']),0,1))." \n";
				}
				echo inputfield('hidden', $namePrefix."[fieldid]",     H($i['fieldid']),
												array('id'=>$marcInputFld.'_fieldid'))." \n";
				echo inputfield('hidden', $namePrefix."[subfieldid]",  H($i['subfieldid']),
												array('id'=>$marcInputFld.'_subfieldid'))." \n";
			}

			$attrs = array("id"=>"$marcInputFld");
			$attrStr = "marcBiblioFld";
			if ( $i['required'] && ($mode != 'onlncol') ) {
				// 'required' does not apply to online data fields
				$attrs['required'] = 'required';
			}
			if ($i['repeat'])
			  	$attrStr .= " rptd";
			else
			  	$attrStr .= " only1";

		  	if ($mode == 'onlnCol')
		    	$attrStr .= " online";
			else
				$attrStr .= " offline";

			$attrs["class"] = $attrStr;

			if ($i['form_type'] == 'textarea') {
				// IE seems to make the font-size of a textarea overly small under
				// certain circumstances.  We force it to a sane value, even
				// though I have some misgivings about it.  This will make
				// the font smaller for some people.
				$attrs["style"] = "font-size:10pt; font-weight: normal;";
				$attrs["rows"] = "7"; $attrs["cols"] = "48";
				echo inputfield('textarea', $namePrefix."[data]", H($i['data']),$attrs, H($i['data']))." \n";
			} else {
			  //$attrs["size"] = "50"; $attrs["maxLength"] = "75";
			  $attrs["size"] = "50"; $attrs["maxLength"] = "256";
				echo inputfield('text', $namePrefix."[data]", H($i['data']),$attrs)." \n";
			}
			echo "</td> \n";
		}
		
		### ============== main code body starts here ==============
		# fetch a complete set of all material types
		$matTypes = new MediaTypes;
		# determine which is to be 'selected'
		if (!empty($_POST['matlCd'])) {
		  	$material_cd_value = $_POST['matlCd'];
		} elseif (!empty($_POST['material_cd'])) {
		  	$material_cd_value = $_POST['material_cd'];
		} elseif (isset($biblio['material_cd'])) {
			$material_cd_value = $biblio['material_cd'];
		} else {
			$material_cd_value = $matTypes->getDefault();
		}

		// get field specs for this material type in 'display postition' order
		$mf = new MaterialFields;
		$fieldSet = $mf->getMatches(array('material_cd'=>$material_cd_value), 'position');

		## anything to process for current media type (material_cd) ?
		//if ($fields->count() == 0) {
        $fields = $fieldSet->fetchAll();
		if (count($fields) == 0) {
			echo '<tr><td colspan="2" >'.T("No fields to fill in.").'</td></tr>\n';
		}

		## build an array of fields to be displayed on user form
		$inputs = array();
		//while (($f=$fields->fetch_assoc())) {
        foreach ($fields as $f) {
		  #  make multiples of those so flagged
			for ($n=0; $n<=$f['repeatable']; $n++) {
				array_push($inputs, mkinput(NULL, NULL, NULL, $f, $n));
			}
		}

		## now build html for those input fields
		foreach ($inputs as $key => $val) {
			$marcInputFld = H($val['tag']).H($val['subfield']);
			echo "<tr> \n";
			echo "	<td valign=\"top\"> \n";
			//if ($val['required']) {
			//	echo '	<sup>*</sup>';
			//}
			echo "		<label for=\"$marcInputFld\">".H($val['label'].":")."</label>";
			if ($val['required']) {
				echo '<span class="reqd">*</span>';
			}
			echo "	</td> \n";

			mkFldSet($key, $val, $marcInputFld, 'editCol');	// normal local edit column
			mkFldSet($key, $val, $marcInputFld, 'onlnCol');  // update on-line column

		echo "</tr> \n";
		}
?>
