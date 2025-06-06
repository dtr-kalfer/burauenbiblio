<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../classes/Date.php"));

/**
 * Creates & displays search criteria forms for Report generator
 * @author Micah Stetson
 */

class Params {
	## ------------------------------------------------------------------------ ##
	public function __construct () {
	}

	public $dict = array();

	## ------------------------------------------------------------------------ ##
	public static function printForm($defs, $prefix='rpt_', $namel=array()) {
		echo "\n".'<table class="'.$prefix.'params">';
		foreach ($defs as $def) {
			$def = array_pad($def, 4, NULL);		# Sigh.
			list($type, $name, $options, $list) = $def;
			$l = array_merge($namel, array($name));
			if (isset($options['repeatable']) && $options['repeatable']) {
				for ($i=0; $i<4; $i++) {
					self::_print($type, array_merge($l, array($i)), $options, $list, $prefix);
				}
			} else {
				self::_print($type, $l, $options, $list, $prefix);
			}
		}
		echo '</table>'."\n";
	}
	public function loadCgi_el($paramdefs, $prefix='rpt_') {
		$params = array();
		$preflen = strlen($prefix);
		foreach ($_REQUEST as $k => $v) {
			if (substr($k, 0, $preflen) == $prefix) {
				$params[substr($k, $preflen)] = $v;
			}
		}
		$el = $this->_load_el($this->dict, $paramdefs, $params);
		$errs = array();
		foreach ($el as $k => $e) {
			if (is_a($e, 'FieldError')) {
				$e->field = $prefix.$e->field;
			}
			$errs[] = $e;
		}
		return $errs;
	}
	public function getDict () {
		return $this->dict;
	}
	public function getList($name) {
		$values = array(array('group', $this->dict));
		foreach ($this->_splitName($name) as $n) {
		 $dicts = array();
			foreach($values as $v) {
				if ($v[0] == 'group') {
					$dicts[] = $v[1];
				}
			}
			$values = array();
			foreach ($dicts as $d) {
				if (isset($d[$n]) and $d[$n]) {
					$v = $d[$n];
					if ($v[0] == 'list') {
						foreach ($v[1] as $val) {
							$values[] = $val;
						}
					} else {
						$values[] = $v;
					}
				}
			}
		}
		return $values;
	}
	public function getFirst($name) {
		$l = $this->getList($name);
		if (isset($l[0])) {
			return $l[0];
		} else {
			return NULL;
		}
	}
	public function getCopy() {
		$p = new Params;
		$p->loadDict($this->dict);
		return $p;
	}

	/* Careful! */
	public function loadDict($dict) {
//echo "Params: in loadDict<br />\n";
		$this->dict = array_merge($this->dict, $dict);
//echo "Params: # dict entries=".count($this->dict)."<br />\n";
	}
	public function load_el($paramdefs, $params) {
		return $this->_load_el($this->dict, $paramdefs, $params);
	}
	public function exists($name) {
		return $this->getFirst($name) != false;
	}
	public function set($name, $type, $value) {
		$path = $this->_splitName($name);
		$n = array_pop($path);
		$dict =& $this->dict;
		foreach ($path as $p) {
			$v =& $dict[$p];
			if ($v[0] == 'list') {
				$v =& $v[1][0];
			}
			if ($v[0] != 'group') {
				$dict[$p] = array('group', array());
				$v =& $dict[$p];
			}
			$dict =& $v[1];
		}
		$dict[$n] = array($type, $value);
	}
	## ------------------------------------------------------------------------ ##

	private static function _print($type, $namel, $options, $list, $prefix) {
		global $loc;
		//assert('$loc');
		assert(!empty($namel));
		if ($type == 'session_id') {
			return;
		}
		if ($type == 'group') {
			echo '<tr>'."\n".'<td class="'.$prefix.'group" colspan="2">'."\n";
			Params::printForm($list, $prefix, $namel);
			echo '</td>'."\n".'</tr>'."\n";
			echo '<tr><td colspan="2"><hr></td></tr>'."\n";
			return;
		}
		if ($type == 'order_by') {
			$title = 'Sort by';
		} elseif (isset($options['title']) && $options['title']) {
			$title = $options['title'];
		} else {
			$title = $namel[count($namel)-1];
		}
		$name = $prefix . array_shift($namel);
		foreach ($namel as $n) {
			$name .= '['.$n.']';
		}
		if (isset($options['default'])) {
			$default = $options['default'];
		} else {
			$default = '';
		}
		echo '<tr class="'.$prefix.'param">';
		echo '<td><label for="'.H($name).'">';
		echo T($title);
		echo '</label>'."\n".'</td>'."\n".'<td>';
		switch ($type) {
/* html5 input types
		case 'number':
			echo inputfield('number', $name, $default);
			break;
		case 'string':
			echo inputfield('text', $name, $default);
			break;
		case 'date':
			echo inputfield('date', $name, $default);
			break;
*/
		case 'select':
			$l = array();
			foreach ($list as $v) {
				list($n, $o) = $v;
				if (isset($o['title']) && $o['title']) {
					$l[$n] = T($o['title']);
				} else {
					$l[$n] = $n;
				}
			}
			echo inputfield('select', $name, $default, NULL, $l);
			break;
		case 'order_by':
			$l = array();
			foreach ($list as $v) {
				list($n, $o) = $v;
				if (isset($o['title']) and $o['title']) {
					$l[$n] = T($o['title']);
				} else {
					$l[$n] = $n;
				}
				$l[$n.'!r'] = $l[$n].' (' . T('Reverse') . ')';
			}
			echo inputfield('select', $name, $default, NULL, $l);
			break;
		default:
			//assert(NULL);
			echo inputfield($type, $name, $default);
		}
		echo '</td>'."\n".'</tr>'."\n";
	}
	private function _splitName($name) {
		return explode('.', $name);
	}
	private function _load_el(&$parameters, $paramdefs, $params, $errprefix=NULL) {
		$errs = array();
		foreach ($paramdefs as $p) {
			$p = array_pad($p, 4, NULL);		# Sigh.
			list($type, $name, $options, $list) = $p;
			if (is_null($errprefix)) {
				$errnm = $name;
			} else {
				$errnm = $errprefix.'['.$name.']';
			}
			if (isset($options['default'])
					and !isset($parameters[$name])
					and !isset($params[$name])) {
				$params[$name] = $options['default'];
			}
			if (isset($parameters[$name]) and !isset($params[$name])) {
				continue;
			}
			if (isset($options['repeatable']) and $options['repeatable']
					and is_array($params[$name])) {
				$l = array();
				foreach ($params[$name] as $idx => $it) {
					list($v, $el) = $this->_mkParam_el($it, $type, $options, $list, $errnm.'['.$idx.']');
					$errs = array_merge($errs, $el);
					if ($v) {
						$l[] = $v;
					}
				}
				if (!empty($l)) {
					$parameters[$name] = array('list', $l);
				} else {
					# A false, but "set" value so that it won't be reset to the default later.
					$parameters[$name] = '';
				}
			} else {
				list($val, $el) = $this->_mkParam_el(isset($params[$name]) ? $params[$name] : null, $type, $options, $list, $errnm);
				$errs = array_merge($errs, $el);
				if ($val) {
					$parameters[$name] = $val;
				} else {
					# A false, but "set" value so that it won't be reset to the default later.
					$parameters[$name] = '';
				}
			}
		}
		return $errs;
	}
	private function _mkParam_el($val, $type, $options, $list, $errprefix) {
		$noerrors = array();
		switch ($type) {
			case 'string':
				$val = trim($val);
				if (strlen($val) != 0) {
					return array(array('string', $val), $noerrors);
				}
				break;
			case 'date':
				$val = trim($val);
				if (!empty($val)) {
					list($val, $error) = Date::read_e($val);
					if ($error) {
						return array(NULL, array(new FieldError($errprefix, $error->toStr())));
					}
					return array(array('string', $val), $noerrors);
				}
				break;
			case 'select':
				foreach ($list as $v) {
					if ($val == $v[0]) {
						return array(array('string', $v[0]), $noerrors);
					}
				}
				break;
			case 'group':
				$dict = array();
				$el = $this->_load_el($dict, $list, $val, $errprefix);
				if (!empty($el)) {
					return array(NULL, $el);
				}
				if (isset($dict[$options['must_have']])
						or !$options['must_have'] and !empty($dict)) {
					return array(array('group', $dict), $noerrors);
				}
				break;
			case 'session_id':
				return array(array('string', session_id()), $noerrors);
			case 'order_by':
				$rawval = $val;
				$desc = ' ';
				if (preg_match('/!r$/', $val)) {
					$desc = ' desc ';
					$val = substr($val, 0, -2);
				}
				$expr = $this->getOrderExpr($val, $list, $desc);
				return array(array('order_by', $expr, $rawval), $noerrors);
			default:
				Fatal::internalError(T("Can't happen"));
		}
		return array(NULL, $noerrors);
	}
	private function getOrderExpr($name, $list, $desc) {
		$expr = false;
		foreach ($list as $v) {
			if ($v[0] != $name) continue;
			if (isset($v[1]['expr']) and $v[1]['expr']) {
				$expr = $v[1]['expr'];
			} else {
				$expr = $name;
			}
			if (!isset($v[1]['type']) or !$v[1]['type']) {
				$v[1]['type'] = 'alnum';
			}
			switch ($v[1]['type']) {
			case 'MARC':
				if (!isset($v[1]['skip_indicator'])) {
					Fatal::internalError(T("MARC sort without skip indicator"));
				}
				$skip = $v[1]['skip_indicator'];
				$expr = "ifnull(substring($expr, $skip+1), $expr)";
				/* fall through */
			case 'alnum':
				$expr = "if($expr regexp '^ *[0-9]', "
								 . "concat('0', ifnull(floor(log10($expr)), 0), $expr), "
								 . "$expr)".$desc;
				break;
			case 'multi':
				$sorts = explode(',', $expr);
				$expr = '';
				foreach ($sorts as $s) {
					$expr .= ', '.$this->getOrderExpr(trim($s), $list, $desc);
				}
				if ($expr) {
					$expr = substr($expr, 2);	# Lose initial ', '
				}
				break;
			default:
				$expr .= $desc;
				break;
			}
			break;
		}
		if (!$expr) {
			return '1'; /* constant expr means no particular order */
		} else {
			return $expr;
		}
	}
}
