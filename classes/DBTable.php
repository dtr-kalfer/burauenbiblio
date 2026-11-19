<?php

require_once(REL(__FILE__, '../classes/Queryi.php'));

abstract class DBTable extends Queryi {
	protected $reqdFlds = array();
  protected $name;
  protected $fields = array();
  protected $reqFields = array();
  protected $key = array();
  protected $sequence = NULL;
  protected $iter = NULL;
  protected $foreign_keys = array();
  protected $types = array(
    'string'=>'%Q',
    'number'=>'%N',
  );

  public function __construct() {
    parent::__construct();
  }

  protected function validate_el($rec, $insert){
    $errors = array();
    // check for missing entries
    foreach ($this->reqFields as $req) {
          if ($insert and !isset($rec[$req])
                          or isset($rec[$req]) and $rec[$req] == '') {
              $errors[] = new FieldError($req, T("Required field missing"));
          }
    }
    return $errors;
  }

  ## ------------------ setters ---------------------------------------- ##
  protected function setName($name) {
    $this->name = $name;
  }
  protected function setFields($fields) {
    $this->fields = $fields;

  }
  protected function setReq($fields) {
    $this->reqFields = $fields;

  }
  protected function setKey() {
    $this->key = func_get_args();
    foreach ($this->key as $k) {
      if (!isset($this->fields[$k])) {
        Fatal::internalError(T("Key field %key% not in field list", array('key'=>$k)));
      echo "sql=$sql<br />\n";
      }
    }
  }
  protected function setForeignKey($key, $table, $field) {
    $this->foreign_keys[] = array('key'=>$key, 'table'=>$table, 'field'=>$field);
  }
  protected function setIter($classname) {
    $this->iter = $classname;
  }
  protected function setSequenceField($sequence) {
    $this->sequence = $sequence;
    if (!isset($this->fields[$sequence])) {
      Fatal::internalError(T("DBTableSequenceField", array('sequence'=>$sequence)));
      echo "sql=$sql<br />\n";
    }
  }

  ## ------------------ getters ---------------------------------------- ##
    public function getName() {return $this->name;}
    public function getFields() {return $this->fields;}
  public function getReqdFlds() {return $this->reqFields;}

  ## ------------------------------------------------------------------------ ##
  public function getKeyList($key, $fields) {
    $sql = $this->mkSQL('SELECT %I FROM %I WHERE ', $key, $this->name)
      . $this->_pairsSql($fields, ' AND ');

    $rslt = $this->_actPrepared($sql, $this->_pairsVals($fields));
        if ($this->iter) {
      $c = $this->iter; # Silly PHP
      return new $c($rslt);
    } else
      return $rslt;
  }
  public function maybeGetOne() {
    $key = func_get_args();
    $sql = $this->mkSQL('SELECT * FROM %I WHERE ', $this->name)
      . $this->_keyTerms($key);

    $row = $this->select01($sql);

    return $row;
  }
  public function getOne() {
    $key = func_get_args();
    $row = call_user_func_array(array($this, 'maybeGetOne'), $key);
    if (is_null($row)) {

            echo "Fatal::internalError";echo "Bad key ";print_r($key);echo" for table $this->name";

    }

    return $row;
  }
  public function getAll($orderby = NULL) {
    $sql = $this->mkSQL('SELECT * FROM %I ', $this->name);
    if (!empty($orderby)) {$sql .= "ORDER BY $orderby";}

        $recs = $this->select($sql);
    return $recs;
  }
  public function getMatches($fields, $orderby=NULL) {
        //echo "in DBTable::getMatches()";print_r($fields);echo "<br />\n";
    $sql = $this->mkSQL('SELECT * FROM %I WHERE ', $this->name)
      . $this->_pairsSql($fields, ' AND ');
    if ($orderby)
      $sql .= $this->mkSQL(' ORDER BY %I ', $orderby);
        //echo "in DBTable::getMatches(), sql= $sql <br />\n";
    $rslt = $this->_actPrepared($sql, $this->_pairsVals($fields));
    if ($this->iter) {
      $c = $this->iter; # Silly PHP
            return new $c($rslt);
    } else {
            return $rslt;
        }
  }
  public function insert($rec, $confirmed=false) {
        list($seqVal, $errors) = $this->insert_el($rec, $confirmed);
    if ($errors) {
            return array(NULL, $errors);
    }
        return array($seqVal, 'Success');
  }
  public function insert_el($rec, $confirmed=false) {

    $this->lock();

    $errs = $this->validate_el($rec, true);
    if ($confirmed) {
      $errs = $this->skipIgnorableErrors($errs);
    }
    if (!empty($errs)) {
      $this->unlock();

      return array(NULL, $errs);
    }

    $sql = $this->mkSQL('INSERT INTO %I SET ', $this->name)
      . $this->_pairsSql($rec);
    //echo "sql = $sql <br />\n";
    $this->_actPrepared($sql, $this->_pairsVals($rec));
    if ($this->sequence) {
      if (isset($rec[$this->sequence])) {
        $seq_val = $rec[$this->sequence];
      } else {
        //echo 'getting auto increment key';
        $seq_val = $this->getInsertID();
      }
    } else {
      $seq_val = NULL;
    }
    $this->unlock();
    return array($seq_val, array());
  }

  public function update($rec, $confirmed=false) {
    $errors = $this->update_el($rec, $confirmed);
    if ($errors) {
            return $errors;
    } else {
          return T("Success");
    }
  }
  public function update_el($rec, $confirmed=false) {
    //echo "in DBTable::update_el()";
    $key = array();
    foreach ($this->key as $k) {
      if (!isset($rec[$k]))
        Fatal::internalError(T("DBTableIncompleteKey", array('key'=>$k)));
      $key[] = $rec[$k];
    }

    $this->lock();
    $errs = $this->checkForeignKeys_el($rec);
    if (!empty($errs)) {
      $this->unlock();
      return $errs;
    }

    $errs = $this->validate_el($rec, false);
    if ($confirmed) {
      $errs = $this->skipIgnorableErrors($errs);
    }
    if (!empty($errs)) {
      $this->unlock();
      return $errs;
    }

    if (!$errs) {
      // $sql = $this->mkSQL('UPDATE %I ', $this->name)
        // . ' SET '.$this->_pairsSql($rec)
        // . ' WHERE '.$this->_keyTerms($key);
      // //echo "sql = $sql<br />\n";
      // $this->_actPrepared($sql, $this->_pairsVals($rec));
      // $this->unlock();
      // return T("Success");
						
			$sql = $this->mkSQL('UPDATE %I SET ', $this->name)
			. $this->_pairsSql($rec)
			. ' WHERE '.$this->_keySql();

			$vals = array_merge(
					$this->_pairsVals($rec),
					$this->_keyVals($key)
			);

			$rslt = $this->_actPrepared($sql, $vals);
    }
  }


	private function _keySql() {
			$terms = array();

			foreach ($this->key as $name) {
					$terms[] = '`'.$this->_ident($name).'` = ?';
			}

			return implode(' AND ', $terms);
	}

	private function _keyVals($key) {
			return array_values($key);
	}

	public function deleteOne() {
			$this->lock();
			$args = func_get_args();

			$sql = $this->mkSQL('DELETE FROM %I WHERE ', $this->name)
					 . $this->_keyTerms($args);

			echo "Attempting SQL: $sql<br>";

			$result = $this->act($sql);
			$this->unlock();

			return $result->rowCount();
	}

  public function deleteOne_member() { // new function retrieved from orig. --F.T.

    $this->lock();
    $sql = $this->mkSQL('DELETE FROM %I WHERE ', $this->name)
      . $this->_keyTerms(func_get_args());
    //echo "sql=$sql<br />\n";
    $result = $this->act($sql);
    $this->unlock();
    return $result->rowCount();
  }

	public function deleteOne_new($bibid = null) { //Added new function for exclusive for bulk delete --F.Tumulak
			$this->lock();

			$sql = $this->mkSQL('DELETE FROM %I WHERE %I = %N', $this->name, 'bibid', $bibid); // set correct input 'bibid' --F.Tumulak

			$result = $this->act($sql);
			$this->unlock();

			echo "Records affected: " . $result->rowCount() . "<br>";
			return $result->rowCount();
	}

  public function deleteMatches($fields) {
    $this->lock();
    $sql = $this->mkSQL('DELETE FROM %I WHERE ', $this->name)
      . $this->_pairsSql($fields, ' AND ');
    $this->_actPrepared($sql, $this->_pairsVals($fields));
    $this->unlock();
  }

  ## ------------------------------------------------------------------------ ##
  protected function checkForeignKeys_el($rec) {
    $errors = array();
    foreach ($this->foreign_keys as $k) {
      if (!isset($rec[$k['key']]) or $rec[$k['key']] == NULL) {
        continue;
      }
      $sql = $this->mkSQL('SELECT * FROM %I '.
          'WHERE %I='.$this->types[$this->fields[$k['key']]],
          $k['table'], $k['field'], $rec[$k['key']]);
      $r = $this->select01($sql);
      if (!$r) {
        $errors[] = new FieldError($k['key'], T("DBTableBadForeignKey", array('key'=>$rec[$k['key']], 'field'=>$k['key'])));
      }
    }
    return $errors;
  }
  private function skipIgnorableErrors($errors) {
    $errs = array();
    foreach ($errors as $e) {
      if (!is_a($e, 'IgnorableError')) {
        $errs[] = $e;
      }
    }
    return $errs;
  }
  private function _keyTerms($key) {
    if (count($key) != count($this->key)) {
      Fatal::internalError(T("Wrong key length"));
    }
    $terms = array();
    for ($i=0; $i<count($key); $i++) {
      $name = $this->key[$i];
      $type = $this->fields[$name];
      $terms[] = $this->mkSQL('%I='.$this->types[$type], $name, $key[$i]);
    }
    return implode(' AND ', $terms);
  }

  private function _pairsSql($rec, $separator=', ') {
    $vals = array();
    foreach ($this->fields as $name => $type) {
      if (isset($rec[$name])) {
        $vals[] = $this->_ident($name).' = ?';
      }
    }
    return implode($separator, $vals);
  }

  private function _pairsVals($rec) {
    $vals = array();
    foreach ($this->fields as $name => $type) {
      if (isset($rec[$name])) {
        $vals[] = $rec[$name];
      }
    }
    return $vals;
  }

  private function _pairs($rec, $separator=', ') {
    $vals = array();
    foreach ($this->fields as $name => $type) {
      if (isset($rec[$name])) {
        $vals[] = $this->_ident($name)." = '".addslashes($rec[$name])."'";
      }
    }
    return implode($separator, $vals);
  }
}