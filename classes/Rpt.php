<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once(REL(__FILE__, "../classes/Queryi.php"));
	require_once(REL(__FILE__, "../classes/Iter.php"));

/**
 * script engine for report generator
 * @author Micah Stetson
 */

class Rpt {

	## ------------------------------------------------------------------------ ##
	public function __construct () {
	}

	public function load_e($filename) {
		$this->_title = $filename;
		$this->_category = 'Misc.';
		$this->_layouts = array();
		$this->_columns = array();
		$this->_paramdefs = array();
		$this->_code = array();
		$this->_interp = NULL;

		$parser = new RptParser;
		list($decls, $err) = $parser->load_e($filename);
		if ($err) {
			return $err;
		}
		foreach ($decls as $decl) {
			list($name, $value) = $decl;
			switch ($name) {
				case 'title':
					$this->_title = $value;
					break;
				case 'category':
					$this->_category = $value;
					break;
				case 'layout':
					$this->_layouts[] = $value;
					break;
				case 'column':
					$this->_columns[] = $value;
					break;
				case 'parameters':
					$this->_paramdefs = array_merge($this->_paramdefs, $value);
					break;
				case 'sql':
					array_push($this->_code, $value);
					break;
				default:
					Fatal::internalError(T("Can't happen"));
			}
		}
//echo "in Rpt::load_e(), params=";print_r($this->_paramdefs);echo"<br />\n";
		return NULL;
//		return $this->getRptCache();
	}
	public function title() {
		return $this->_title;
	}
	public function paramDefs() {
		return $this->_paramdefs;
	}
	public function select($params) {
		return new RptIter($this->_code, $params);
	}
	public function columns() {
		return $this->_columns;
	}
	public function category() {
		return $this->_category;
	}
	public function layouts() {
		return $this->_layouts;
	}
	public function getRptCache() {
		return array($this->title, $this->_paramdefs, $this->_columns, $this->_category, $this->_layouts);
	}
}

# Grammar for the report language (yacc style,
# capital words and quoted strings are lexer tokens):
#
# decls:	  /* empty */
#		| decls decl
#		;
# decl:	  'title' WORD
#		| 'category' WORD
#		| 'layout' WORD params
#		| 'column' WORD params
#		| 'parameters' param_decls end
#		| 'sql' sql_form
#		;
# param_decls:	  /* empty */
#			| param_decls 'order_by' params items end
#			| param_decls 'session_id' params
#			| param_decls param_decl
#			;
# param_decl:	  'string' WORD params
#			| 'date' WORD params
#			| 'group' WORD params param_decls end
#			| 'select' WORD params items end
#			;
# items:	  /* empty */
#		| items 'item' WORD params
#		| items 'sql' SQLCODE end
#		;
# sql_form:	  sql_exprs subselects end
#			;
# sql_exprs:	  /* empty */
#			| sql_exprs sql_expr
#			;
# sql_expr:	  SQLCODE
#			| PARAMREF
#			| 'if_set' WORD sql_exprs else_part
#			| 'if_equal' WORD WORD sql_exprs else_part
#			| 'if_not_equal' WORD WORD sql_exprs else_part
#			| 'foreach_parameter' WORD sql_exprs end
#			| 'foreach_word' WORD sql_exprs end
#			| 'order_by_expr'
#			;
# else_part:	  'else' sql_exprs end
#			| end
#			;
# subselects:	  /* empty */
#			| subselects 'subselect' WORD sql_form
#			;
# end:	  'end' params
#		;
# words:	  WORD
#		| words WORD
#		;
# params:	  /* empty */
#		| params WORD '=' WORD
#		| params WORD
#		;
#
class RptParser {
	public function load_e($filename) {
		# returns true or an error object
		$this->filename = $filename;

		$this->fd = fopen($this->filename, 'rb');
		if ($this->fd === FALSE) {
			return array(NULL, new OBErr(T("Cannot open file: %file%", array('file'=>$this->filename))));
		}
		$this->_tokens = array();
		$this->line = 0;
		$this->lex();
		return $this->parse_e();
	}
	private function parse_e() {
		$list = array();
		while($d = $this->p_decl()) {
			if (is_a($d, 'Error')) {
				return array(NULL, $d);
			}
			array_push($list, $d);
		}
		if ($this->lat[0] == 'ERROR') {
			return array(NULL, $this->lexerError());
		}
		if ($this->lat[0] != 'EOF') {
			return array(NULL, $this->unexpectedToken());
		}
		return array($list, NULL);
	}

	private function error($msg) {
		return new OBErr($this->filename.':'.$this->line.': '.$msg);
	}
	private function lexerError() {
		return $this->error('Lexer error - FIXME');
	}
	private function unexpectedToken($expected=NULL) {
		if ($this->lat[0] == 'EOF') {
			$str = T("Unexpected end of file");
		} else {
			$str = T("Unexpected token '%token%'", array('token'=>$this->lat[0]));
		}
		if ($expected) {
			$str .= ' '.T("expecting '%exp%'", array('exp'=>$expected));
		}
		return $this->error($str);
	}

	/*
	 * Lexical analyser
	 */
	private function lex() {
		$this->lat = $this->_lex();
	}
	private function _lex() {
		if (!empty($this->_tokens)) {
			return array_shift($this->_tokens);
		}
		# NOTE: Lines longer than 4096 bytes will mess things up.
		while ($line = fgets($this->fd, 4096)) {
			$this->line++;
			if ($line == "\n" or $line == "\r\n" or $line == "\r" or $line[0] == '#') { // fixed deprecated,
				continue;
			}
			if ($line[0] == '.') { // fixed deprecated, Array and string offset access syntax with curly braces --F.Tumulak
				$this->_tokens = $this->getCmdTokens(trim(substr($line, 1)));
			} else {
				$this->_tokens = $this->getSqlTokens(trim($line));
			}
			if (!empty($this->_tokens)) {
				return array_shift($this->_tokens);
			}
		}
		if ($line === FALSE and !feof($this->fd)) {
			return array('ERROR');
		}
		return array('EOF');
	}
	private function getCmdTokens($str) { // recalibrate this function for v8.0 compatible --F.Tumulak
		$cmds = array('title', 'category', 'layout', 'column', 'parameters', 'sql',
			'order_by', 'session_id', 'string', 'date', 'group', 'select', 'item',
			'if_set', 'if_equal', 'if_not_equal',
			'foreach_parameter', 'foreach_word',
			'else', 'subselect', 'end', 'order_by_expr');
		$list = array();
		while (!empty($str)) {
				if ($str[0] == ' ' || $str[0] == "\t") {
						$str = substr($str, 1);
						continue;
				}

				if (ctype_alnum($str[0])) {
						$w = '';
						while (!empty($str) && (ctype_alnum($str[0]) || $str[0] == '_')) {
								$w .= $str[0];
								$str = substr($str, 1);
						}
						array_push($list, array('WORD', $w));

				} else if ($str[0] == '"' || $str[0] == '\'') {
						list($w, $str) = $this->getQuoted($str);
						array_push($list, array('WORD', $w));

				} else {
						array_push($list, array($str[0]));
						$str = substr($str, 1);
				}
		}

		if (
				isset($list[0]) &&
				is_array($list[0]) &&
				isset($list[0][0], $list[0][1]) &&
				$list[0][0] === 'WORD' &&
				in_array($list[0][1], $cmds)
		) {
				$list[0] = array($list[0][1]);
		}

		return $list;
	}
	private function getQuoted($str) {
		if (empty($str)) {
			Fatal::internalError(T("getQuoted() called with empty %str%",array('str'=>$str)));
		}
		$q = $str[0];
		$w = '';
		for ($n=1; $n < strlen($str); $n++) {
			if ($str[$n] == $q) {
				break;
			}
			if ($str[$n] == '\\') {
				$n++;
				if ($n >= strlen($str)) {
					break;
				}
			}
			$w .= $str[$n];
		}
		return array($w, substr($str, $n+1));
	}
	private function getSqlTokens($str) {
		static $conversions = array('!' => '%!', '#' => '%N', '"' => '%q', '.' => '%I', '`' => '%i');
		$list = array();
		$sql = '';
		while (!empty($str)) {
			$p = strpos($str, "%");
			if ($p === false) {
				$sql .= $str;
				break;
			}
			$sql .= substr($str, 0, $p);
			$str = substr($str, $p+1);		// Skip '%'
			$p = strpos($str, "%");
			if ($p === false) {
				# FIXME - there should be a way to error
				$ref = substr($str, 0);
				$str = '';
			} else {
				$ref = substr($str, 0, $p);
				$str = substr($str, $p+1);		// Skip '%'
			}
			if ($ref == '') {				// %%
				$sql .= '%';
			} else {
				if (!empty($sql)) {
					array_push($list, array('SQLCODE', $sql));
					$sql = '';
				}
				if (array_key_exists($ref[0], $conversions)) {
					$conv = $conversions[$ref[0]];
					$ref = substr($ref, 1);
				} else {
					$conv = '%Q';
				}
				array_push($list, array('PARAMREF', array($ref, $conv)));
			}
		}
		# hack to force proper whitespace
		$sql .= ' ';
		array_push($list, array('SQLCODE', $sql));
		return $list;
	}

	/*
	 * Parser
	 */
	private function p_decl() {
		$t = $this->lat[0];
		switch ($t) {
			case 'title':
			case 'category':
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$decl = array($t, $this->lat[1]);
				$this->lex();
				return $decl;
			case 'layout':
			case 'column':
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$name = $this->lat[1];
				$this->lex();
				$list = $this->p_params();
				if (is_a($list, 'Error')) {
					return $list;
				}
				$list['name'] = $name;
				return array($t, $list);
			case 'parameters':
				$this->lex();
				$list = $this->p_param_decls();
				if (is_a($list, 'Error')) {
					return $list;
				}
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				return array('parameters', $list);
			case 'sql':
				$this->lex();
				return $this->p_sql_form();
			default:
				return false;
		}
	}
	private function p_param_decls() {
		$list = array();
		while (1) {
			if ($this->lat[0] == 'order_by') {
				$this->lex();
				$params = $this->p_params();
				if (is_a($params, 'Error')) {
					return $params;
				}
				$items = $this->p_items();
				if (is_a($items, 'Error')) {
					return $items;
				}
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				$list[] = array('order_by', 'order_by', $params, $items);
			} elseif ($this->lat[0] == 'session_id') {
				$this->lex();
				$params = $this->p_params();
				if (is_a($params, 'Error')) {
					return $params;
				}
				$list[] = array('session_id', 'session_id', $params);
			} else {
				$d = $this->p_param_decl();
				if (is_a($d, 'Error')) {
					return $d;
				} elseif (!$d) {
					break;
				}
				$list[] = $d;
			}
		}
		return $list;
	}
	private function p_param_decl() {
		if (!in_array($this->lat[0], array('string', 'date', 'group', 'select'))) {
			return false;
		}
		$type = $this->lat[0];
		$this->lex();
		if ($this->lat[0] != 'WORD') {
			return $this->unexpectedToken('WORD');
		}
		$name = $this->lat[1];
		$this->lex();
		$params = $this->p_params();
		if (is_a($params, 'Error')) {
			return $params;
		}
		switch ($type) {
			case 'string':
			case 'date':
				return array($type, $name, $params);
			case 'group':
				$list = $this->p_param_decls();
				if (is_a($list, 'Error')) {
					return $list;
				}
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				return array('group', $name, $params, $list);
			case 'select':
				$list = $this->p_items();
				if (is_a($list, 'Error')) {
					return $list;
				}
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				return array('select', $name, $params, $list);
			default:
				Fatal::internalError(T("Can't happen"));
		}
	}
	private function p_items() {
		$list = array();
		while (1) {
			if ($this->lat[0] == 'item') {
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$value = $this->lat[1];
				$this->lex();
				$params = $this->p_params();
				if (is_a($params, 'Error')) {
					return $params;
				}
				array_push($list, array($value, $params));
			} else if ($this->lat[0] == 'sql') {
				$this->lex();
				if ($this->lat[0] != 'SQLCODE') {
					return $this->unexpectedToken('SQLCODE');
				}
				$sql = $this->lat[1];
				$this->lex();
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				$q = new Query();
				$r = $q->select($sql);
				while ($row = $r->fetch_assoc()) {
					array_push($list, array($row['value'], $row));
				}
			} else {
				break;
			}
		}
		return $list;
	}
	private function p_sql_form() {
		$exprs = $this->p_sql_exprs();
		if (is_a($exprs, 'Error')) {
			return $exprs;
		}
		$subs = $this->p_subselects();
		if (is_a($subs, 'Error')) {
			return $subs;
		}
		$result = $this->p_end();
		if (is_a($result, 'Error')) {
			return $result;
		}
		return array('sql', array($exprs, $subs));
	}
	private function p_sql_exprs() {
		$list = array();
		while ($e = $this->p_sql_expr()) {
			if (is_a($e, 'Error')) {
				return $e;
			}
			array_push($list, $e);
		}
		return $list;
	}
	private function p_sql_expr() {
		switch ($this->lat[0]) {
			case 'SQLCODE':
				$code = $this->lat[1];
				$this->lex();
				return array('sqlcode', $code);
			case 'PARAMREF':
				list($name, $conv) = $this->lat[1];
				$this->lex();
				return array('value', $name, $conv);
			case 'if_set':
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$name = $this->lat[1];
				$this->lex();
				$then = $this->p_sql_exprs();
				if (is_a($then, 'Error')) {
					return $then;
				}
				$else = $this->p_else_part();
				if (is_a($else, 'Error')) {
					return $else;
				}
				return array('if_set', $name, $then, $else);
			case 'if_equal':
			case 'if_not_equal':
				$type = $this->lat[0];
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$name = $this->lat[1];
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$value = $this->lat[1];
				$this->lex();
				$then = $this->p_sql_exprs();
				if (is_a($then, 'Error')) {
					return $then;
				}
				$else = $this->p_else_part();
				if (is_a($else, 'Error')) {
					return $else;
				}
				return array($type, $name, $value, $then, $else);
			case 'foreach_parameter':
			case 'foreach_word':
				$type = $this->lat[0];
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$name = $this->lat[1];
				$this->lex();
				$block = $this->p_sql_exprs();
				if (is_a($block, 'Error')) {
					return $block;
				}
				$result = $this->p_end();
				if (is_a($result, 'Error')) {
					return $result;
				}
				return array($type, $name, $block);
			case 'order_by_expr':
				$tok = $this->lat[0];
				$this->lex();
				return array($tok);
			default:
				return false;
		}
	}
	private function p_else_part() {
		if ($this->lat[0] == 'else') {
			$this->lex();
			$list = $this->p_sql_exprs();
			if (is_a($list, 'Error')) {
				return $list;
			}
		} else {
			$list = array();
		}
		$result = $this->p_end();
		if (is_a($result, 'Error')) {
			return $result;
		}
		if (!$result) {
			return $this->unexpectedToken('end');
		}
		return $list;
	}
	private function p_subselects() {
		$list = array();
		while ($this->lat[0] == 'subselect') {
			$this->lex();
			if ($this->lat[0] != 'WORD') {
				return $this->unexpectedToken('WORD');
			}
			$name = $this->lat[1];
			$this->lex();
			$s = $this->p_sql_form();
			if (is_a($s, 'Error')) {
				return $s;
			}
			$list[$name] = $s;
		}
		return $list;
	}
	private function p_end() {
		if ($this->lat[0] != 'end') {
			return $this->unexpectedToken('end');
		}
		$this->lex();
		$list = $this->p_params();
		if (is_a($list, 'Error')) {
			return $list;
		}
		return true;
	}
	private function p_words() {
		$list = array();
		while ($this->lat[0] == 'WORD') {
			array_push($list, $this->lat[1]);
			$this->lex();
		}
		return $list;
	}
	private function p_params() {
		$params = array();
		while ($this->lat[0] == 'WORD') {
			$name = $this->lat[1];
			$this->lex();
			if ($this->lat[0] == '=') {
				$this->lex();
				if ($this->lat[0] != 'WORD') {
					return $this->unexpectedToken('WORD');
				}
				$value = $this->lat[1];
				$this->lex();
			} else {
				$value = true;
			}
			$params[$name] = $value;
		}
		return $params;
	}
}

class RptIter extends Iter {
	# $sqls is a list of tuples of array($code, $subselects).
	# $code contains the code elements which construct a single
	# SQL query.  $subselects is a list of lists of code elements.  Each
	# toplevel list contains the code elements which construct a single
	# SQL subselect query.
	# A code element is an array in one of the following forms:
	#	array('sqlcode', $sql)
	#		$sql is SQL text to be appended to the query
	#	array('value', $name, $conv)
	#		$name is a parameter name whose value should
	#		be substituted into the query using mkSQL conversion
	#		$conv.
	#	array('if_set', $name, $then, $else)
	#		$then and $else are lists of code elements.  $then
	#		is evaluated if the parameter named $name is set,
	#		$else otherwise.
	#	array('if_equal', $name, $value, $then, $else)
	#		$then and $else are lists of code elements.  $then
	#		is evaluated if the first value of the parameter named
	#		$name is equal to the string $value, $else otherwise.
	#	array('if_not_equal', $name, $value, $then, $else)
	#		$then and $else are lists of code elements.  $then
	#		is evaluated if the first value of the parameter named
	#		$name is not equal to the string $value, $else otherwise.
	#	array('foreach_parameter', $name, $block)
	#		For each value in the parameter list named $name,
	#		the list of code elements in $block is evaluated with
	#		the parameter $name set to each successive value of
	#		$name.
	#	array('foreach_word', $name, $block)
	#		Splits the value of the parameter named $name into
	#		words and, for each word, evaluates the list of code
	#		elements in $block with the parameter $name set to
	#		each word in turn.
	#	array('order_by_expr')
	#		An appropriate SQL ORDER BY clause is appended to
	#		the query at this point.

	# These are private.
	private $params;
	private $iter;
	private $subselects;
	## ------------------------------------------------------------------------ ##

	public function __construct($sqls, $params) {
		$this->params = $params;
		$this->q = new Queryi();
		foreach ($sqls as $s) {
			list($code, $subs) = $s;
			$sql = $this->_exec($code, $params);
			# I don't like having to differentiate selects here.  It might be
			# better for the Rpt syntax to indicate whether a query is expected
			# to return rows or not.
			if (strncasecmp(trim($sql), 'select', strlen('select')) != 0) {
				//$this->q->act($sql);
				$rslts = $this->q->act($sql);
			} else {
				//$this->iter = $this->q->select($sql);
				$rslts = $this->q->select($sql);
				$this->subselects = $subs;
			}
			$this->iter = $rslts;
		}
	}
	public function count() {
		//return $this->iter->num_rows;
		//return count($this->iter);
		$recs = $this->iter->fetchAll();
		return count($recs);
	}
	public function skip() {
    	//$this->iter->fetch_assoc();
    	$this->iter->fetch();
	}
	public function next() {
		//$row = $this->iter->fetch_assoc();
		$row = $this->iter->fetch();
		//if ($row === NULL) {
		if (empty($row)) {
			return $row;
		}
		$scope = $this->params->getCopy();
		foreach ($row as $n => $v) {
			$scope->set($n, 'string', $v);
		}
		foreach ($this->subselects as $name => $sql) {
			if ($sql[0] != 'sql') {
				Fatal::internalError(T("Broken RPT code structure"));
			}
			$iter = new RptIter(array($sql[1]), $scope);
			$row[$name] = $iter->toArray();
		}
		return $row;
	}
	## ------------------------------------------------------------------------ ##

	private function _exec($code, $scope) {
		$query = '';
		foreach ($code as $c) {
			switch ($c[0]) {
				case 'sqlcode':
					list( , $sql) = $c;
					$query .= $sql;
					break;
				case 'value':
					list( , $name, $conv) = $c;
					list($type, $value) = $scope->getFirst($name);
					$query .= $this->q->mkSQL($conv, $value);
					break;
				case 'if_set':
					list( , $name, $then, $else) = $c;
					if ($scope->exists($name)) {
						$query .= $this->_exec($then, $scope);
					} else {
						$query .= $this->_exec($else, $scope);
					}
					break;
				case 'if_equal':
					list( , $name, $value, $then, $else) = $c;
					if (!$scope->exists($name)) {
						$query .= $this->_exec($else, $scope);
					} else {
						list($t, $v) = $scope->getFirst($name);
						if ($v == $value) {
							$query .= $this->_exec($then, $scope);
						} else {
							$query .= $this->_exec($else, $scope);
						}
					}
					break;
				case 'if_not_equal':
					list( , $name, $value, $then, $else) = $c;
					if (!$scope->exists($name)) {
						$query .= $this->_exec($then, $scope);
					} else {
						list($t, $v) = $scope->getFirst($name);
						if ($v != $value) {
							$query .= $this->_exec($then, $scope);
						} else {
							$query .= $this->_exec($else, $scope);
						}
					}
					break;
				case 'foreach_parameter':
				case 'foreach_word':
					list($type, $name, $block) = $c;
					if ($type == 'foreach_parameter') {
						$vlist = $scope->getList($name);
					} else {
						include_once(REL(__FILE__, "../classes/Search.php"));
						list($t, $v) = $scope->getFirst($name);
						if ($t != "string") {
							Fatal::internalError(T("'%t%' is not a string",array('t'=>$t)));
						}
						$vlist = array();
						foreach (Search::explodeQuoted($v) as $w) {
							$vlist[] = array('string', $w);
						}
					}
					foreach ($vlist as $v) {
						list($type, $value) = $v;
						$s = $scope->copy();
						$s->set($name, $type, $value);
						$query .= $this->_exec($block, $s);
					}
					break;
				case 'order_by_expr':
					if ($v = $scope->getFirst('order_by')) {
						list($type, $value, $raw) = $v;
						if ($type != "order_by") {
							Fatal::internalError(T("'%type%' must be 'order_by'"),array('type'=>$type));
						}
						$query .= 'order by '.$value.' ';
					}
					break;
				default:
					Fatal::internalError(T("Can't happen"));
					break;
			}
		}
		return $query;
	}
}
