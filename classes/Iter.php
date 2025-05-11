<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Classes on this page should probably be re-done as Interfaces to Iterator
 * @author Micah Stetson
 *
 * note: may no longer be needed with added ability of PHP's mysqli API - FL
 * issue really in doubt with PHP features plus use of PDO. - FL May 2016
 */

class Iter {
	# All subclasses must implement this method.
	function next() {
		# Returns the next item in the sequence or NULL if no more items.
		return NULL;
	}
	function count() {
		# Returns an integer or NULL if not applicable.
		# The return value of this function is intended to indicate the total
		# number of results of the operation the Iter represents.
		return NULL;
	}
	function skip() {
		# Discards the next item in the sequence.
		# This method is meant to sidestep any expensive processing
		# a subclass might perform as part of next().
		$this->next();
	}
	function toArray() {
		# Returns an array containing all the elements in the Iter.
		# This may use up all your RAM if you aren't careful.
		$arr = array();
		while (($i = $this->next()) !== NULL) {
			$arr[] = $i;
		}
		return $arr;
	}
}

class ArrayIter extends Iter {
	function __construct($arr) {
		$this->arr = $arr;
		$this->i = 0;
	}
	function count() {
		return count($this->arr);
	}
	function next() {
		if (!isset($this->arr[$this->i])) {
			return NULL;
		}
		return $this->arr[$this->i++];
	}
	function skip() {
		$this->i++;
	}
}

class MapIter extends Iter {
	function __construct($callback, $iter) {
		$this->callback = $callback;
		$this->iter = $iter;
	}
	function count() {
		return $this->iter->count();
	}
	function next() {
		$i = $this->iter->next();
		if ($i === NULL) {
			return $i;
		} else {
			return call_user_func($this->callback, $i);
		}
	}
	function skip() {
		$this->iter->next();
	}
}

class NumberedIter extends Iter {
	function __construct($iter) {
		$this->iter = $iter;
		$this->n = 0;
	}
	function count() {
		return $this->iter->count();
	}
	function next() {
		$r = $this->iter->next();
		if (is_array($r)) {
			$r['.seqno'] = $this->n++;
		}
		return $r;
	}
	function skip() {
		$this->iter->skip();
		$this->n++;
	}
}

class SliceIter extends Iter {
	function __construct($skip, $len, $iter) {
		for ($i=0; $i < $skip; $i++) {
			$iter->skip();
		}
		$this->iter = $iter;
		$this->len = $len;
	}
	function count() {
		return $this->iter->count();
	}
	function next() {
		if ($this->len <= 0) {
			return NULL;
		}
		$this->len--;
		return $this->iter->next();
	}
	function skip() {
		$this->len--;
		$this->iter->skip();
	}
}
