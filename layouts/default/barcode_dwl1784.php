<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_barcode_dwl1784 {
	var $p;
	function paramDefs() {
		return array(
			array('string', 'skip', array('title'=>'Skip Labels', 'default'=>'0')),
		);
	}
	function init($params) {
		$this->p = $params;
	}
	function render($rpt) {
		$lay = new Lay(array(0 => '21cm', 1 => '29.7cm'));
			$lay->container('Lines', array(
				'margin-top'=>'14.5mm', 'margin-bottom'=>'10.5mm',
				'margin-left'=>'7.2mm', 'margin-right'=>'7.2mm',
				'x-spacing'=>'2.5mm'
			));
				$lay->container('Columns');
					list(, $skip) = $this->p->getFirst('skip');
					for ($i = 0; $i < $skip; $i++) {
						$lay->container('Column', array(
							'height'=>'34.9mm', 'width'=>'63.5',
						));
						$lay->close();
					}
					while ($row = $rpt->each()) {
						$lay->container('Column', array(
							'height'=>'33.9mm', 'width'=>'63.5mm',
							'y-align'=>'center',
						));
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Times-Bold', 10);
									if (strlen($row['library_name']) > 35) {
										$row['library_name'] = substr($row['library_name'], 0, 35)."...";
									}
									$lay->text($row['library_name']);
								$lay->popFont();
							$lay->close();
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Times-Roman', 9);
									if (strlen($row['title']) > 38) {
										$row['title'] = substr($row['title'], 0, 38)."...";
									}
									$lay->text($row['title']);
								$lay->popFont();
							$lay->close();
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Times-Roman', 8);
									if (strlen($row['author']) > 40) {
										$row['author'] = substr($row['author'], 0, 40)."...";
									}
									$lay->text($row['author']);
								$lay->popFont();
							$lay->close();
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Code39JK', 30);
									$lay->text('*'.strtoupper($row['barcode_nmbr']).'*');
								$lay->popFont();
							$lay->close();
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Courier', 12);
									$lay->text(strtoupper($row['barcode_nmbr']));
								$lay->popFont();
							$lay->close();
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Times-Roman', 8);
									$site = "Lib.: " . $row['site_code'];
									$class = "Class: " . $row['callno'];
									$lay->text(str_pad($site , 40 - strlen($class), ".") . $class);
								$lay->popFont();
							$lay->close();
						$lay->close();
					}
				$lay->close();
			$lay->close();
		$lay->close();
	}
}
