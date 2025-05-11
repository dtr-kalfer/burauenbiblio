<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Links.php"));
/**
 * This class converts the raw report data into displayable materail
 *@author Micah Stetson
 */

class ReportDisplay {
	public function __construct($rpt) {
		$this->rpt = $rpt;
	}
	function columns($url=NULL) {
		$cl = array();
		list($type, $exp, $order) = $this->rpt->params->getFirst('order_by');
		foreach ($this->rpt->columns() as $col) {
			if (isset($col['hidden']) and $col['hidden']) {
				continue;
			}
			$c = array();
			if ($url && $col['sort']) {
				if ($order == $col['sort']) {
					$href = $url->get($col['sort'].'!r');
					$img = " <img border='0' src='../images/down.png' alt='&darr;'>"."\n";
				} else if ($order == $col['sort'].'!r') {
					$href = $url->get($col['sort']);
					$img = " <img border='0' src='../images/up.png' alt='&uarr;'>"."\n";
				} else {
					$href = $url->get($col['sort']);
					$img = "";
				}
				$c['title'] = '<a href="'.$href.'">'.H($col['title']).'</a>'.$img."\n";
			} else {
				$c['title'] = H($col['title']);
			}
			if (isset($col['align'])) {
				$c['align'] = $col['align'];
			}
			$cl[] = $c;
		}
		return $cl;
	}
	function row($row) {
		$r = array();
		foreach ($this->rpt->columns() as $col) {
			if (isset($col['hidden']) and $col['hidden']) {
				continue;
			}
/*
			if ($col['func'] and in_array($col['func'], get_class_methods('ReportDisplayFuncs'))) {
				$r[] = ReportDisplayFuncs::$col['func']($col, $row, $this->rpt);
			} else {
*/
				$r[] = $row[$col['name']];
			//}
		}
		return $r;
	}
	function pages($url, $currPage) {
		$pageCount = ceil($this->rpt->count()/Settings::get('items_per_page'));
		if ($pageCount <= 1) {
			return '';
		}
		$s = '<div class="pageAvail">'."\n";
		$s .= T("Pages").': ';
		if ($currPage > 1) {
			$s .= '<a href="'.$url->get($currPage-1).'" class="prevpage">'.T("Prev").'</a> '."\n";
			//$s .= '<input type="button" class="prevpage" value="'.T("Previous Page").'">'."\n";
		}
		$i = min($pageCount-OBIB_SEARCH_MAXPAGES + 1,
						 $currPage-OBIB_SEARCH_MAXPAGES/2 + 1);
		$i = max($i, 1);
		$maxPg = min(OBIB_SEARCH_MAXPAGES+$i - 1, $pageCount);
		if ($i > 1) {
			$s .= "... ";
		}
		for (;$i <= $maxPg; $i++) {
			if ($i == $currPage) {
				$s .= "<b>".H($i)."</b> "."\n";
			} else {
				$s .= '<a href="'.$url->get($i).'">'.H($i).'</a>'."\n";
			}
		}
		if ($maxPg < $pageCount) {
			$s .= "... ";
		}
		if ($currPage < $pageCount) {
			$s .= '<a href="'.$url->get($currPage+1).'" class="nextpage">'.T("Next").'</a>'."\n";
			//$s .=	'<input type="button" class="nextpage" value="'.T("Next Page").'">'."\n";
		}
		$s .= '</div>'."\n";
		return $s;
	}
}

class ReportDisplayFuncs {
	function raw($col, $row, $rpt) {
		return $row[$col['name']];
	}
	function _link_common($col, $row, $rpt, $id, $url) {
		if ($rpt->name) {
			$params = array(
				'rpt' => $rpt->name,
				'seqno' => $row['.seqno'],
			);
		}
		$s = '<a ';
		if ($col['link_class']) {
			$s .= 'class="'.$col['link_class'].'" ';
		}
		$s .= 'href="'.$url->get($id, $params).'">'.H($row[$col['name']]).'</a>'."\n";
		return $s;
	}
	function item_cart_add($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['bibid'],
			new LinkUrl('../shared/cart_add.php', 'id[]', array(
				'name'=>'bibid',
			))
		);
	}
	function item_cart_del($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['bibid'],
			new LinkUrl('../shared/cart_del.php', 'id[]', array(
				'name'=>'bibid',
			))
		);
	}
	function biblio_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['bibid'],
			new LinkUrl('../catalog/srchForms.php', 'bibid', array('tab'=>'rpt'))
		);
	}
	function booking_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['bookingid'],
			new LinkUrl('../circ/booking_view.php', 'bookingid', array())
		);
	}
	function member_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['mbrid'],
			new LinkUrl('../circ/memberForms.php', 'mbrid', array('tab'=>'rpt'))
		);
	}
	function site_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['siteid'],
			new LinkUrl('../admin/sitesForm.php', 'siteid', array())
		);
	}
	function calendar_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row['calendar'],
			new LinkUrl('../admin/calendarForm.php', 'calendar', array())
		);
	}
	function subject_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row[$col['name']],
			new LinkUrl('../catalog/srchForms.php', 'searchText', array(
				'searchType'=>'subject',
				'exact'=>1,
			))
		);
	}
	function series_link($col, $row, $rpt) {
		return ReportDisplayFuncs::_link_common($col, $row, $rpt, $row[$col['name']],
			new LinkUrl('../catalog/srchForms.php', 'searchText', array(
				'searchType'=>'series',
				'exact'=>1,
			))
		);
	}
	function checkbox($col, $row, $rpt) {
		assert('$col["checkbox_name"] != NULL');
		assert('$col["checkbox_value"] != NULL ');
		$s = '<input type="checkbox" ';
		$s .= 'name="'.H($col['checkbox_name']).'" ';
		$s .= 'value="'.H($row[$col['checkbox_value']]).'" ';
		if ($col['checkbox_checked'] === true) {
			$s .= 'checked="checked" ';
		} elseif (is_string($col['checkbox_checked'])) {
			if (strtolower($row[$col['checkbox_checked']]) == 'y') {
				$s .= 'checked="checked" ';
			}
		}
		$s .= '/>'."\n";
		return $s;
	}
	function select($col, $row, $rpt) {
		assert('$col["select_name"] != NULL');
		assert('$col["select_index"] != NULL');
		assert('$col["select_key"] != NULL');
		assert('$col["select_value"] != NULL ');
		$name = $col['select_name'].'['.$row[$col['select_index']].']';
		$data = array();
		foreach ($row[$col['name']] as $r) {
			$data[$r[$col['select_key']]] = $r[$col['select_value']];
		}
		if (isset($col['select_selected']) and isset($row[$col['select_selected']])) {
			$selected = $row[$col['select_selected']];
		} else {
			$selected = '';
		}
		return inputfield('select', $name, $selected, NULL, $data);
	}
	function member_list($col, $row, $rpt) {
		$s = '';
//		$t = 'href="../circ/mbr_view.php?mbrid';
		$t = 'href="../circ/memberForms.php?mbrid';
		foreach ($row[$col['name']] as $m) {
			$s .= '<a '.$t.'='.HURL($m['mbrid']).'">'
						. H($m['first_name']).' '.H($m['last_name']).' ('.H($m['site_code']).')'
						. '</a>, '."\n";
		}
		return substr($s, 0, -2);  # lose the final ', '
	}
}
