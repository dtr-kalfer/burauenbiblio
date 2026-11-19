<?php

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/DbCore.php"));

class Queryi {
  private $lockDepth;

  public function __construct() {
    $this->lockDepth = 0;
        //echo "in Queryi::__construct() <br />\n";
    }

    protected function getDSN () {
        $fn = '../dbParams.php';
        if (file_exists($fn) ) {
            // DO NOT change to 'Include Once' !!!!!
            include($fn);
        } else {
            $this->dsn['host'] = 'localhost';
            $this->dsn['username'] = 'admin';
            $this->dsn['pwd'] = 'admin';
            $this->dsn['database'] = 'xxxopenbiblioxxx';
            $this->dsn['mode'] = 'nodb';
            $this->dsn["mode"] = 'haveconst';
        }
    }
  public function act($sql) {
    //$this->lock();
    $results = $this->_act($sql);
    //$this->unlock();
    return $results;
  }
  public function select($sql) {
    $results = $this->_act($sql);
    if (!isset($results)) {
      return T("NothingFoundError");
    }
    return $results;
  }

  public function select1($sql) {
    $rslt = $this->select($sql);
        $recs = $rslt->fetchAll();
        $nRecs = count($recs);
        if ($nRecs != 1) {
      return null;
    } else {
            return $recs[0];
    }
  }

  public function select01_new($sql) { // Added a new function for 'Add item to cart' --F.Tumulak
    $core = DbCore::getInstance();
    $r = $core->dbh->query($sql);
    if ($r === false) return false;
    return $r->fetch(); // returns array or false
  }

  public function select01($sql) {
    $r = $this::act($sql);
            return $r->fetch();
  }

private function set_encoding() {
    $core = DbCore::getInstance();
    $r = $core->dbh->query("SELECT value FROM settings where name='charset'");
    if ($r->num_rows == 1) {
        $row = $r->fetch_assoc();
        if (!$this->dbh->set_charset($row['value']))  {
            $this->dbh->set_charset('utf8');
        }
    } else {
        $this->dbh->set_charset('utf8');
    }
}
  private function _act($sql) {
        $core = DbCore::getInstance();
    $r =  $core->dbh->query($sql);
    if ($r === false) {
      return 'Error: '.T("Database query failed");
    }
    return $r;
  }

protected function _actPrepared($sql, array $vals = array()) {
    $core = DbCore::getInstance();

    try {
        $stmt = $core->dbh->prepare($sql);

        foreach ($vals as $i => $v) {
            $stmt->bindValue($i + 1, $v, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt;

    } catch (PDOException $e) {
        error_log("Database prepared query failed: " . $e->getMessage());
        return false;
    }
}

  protected function getInsertID() {

        // this works for mySQL; others ???????
        $core = DbCore::getInstance();
        $rslt = $core->dbh->query('SELECT LAST_INSERT_ID() as last_id');
        $id = $rslt->fetchAll();
        $last_id = intval($id[0]['last_id']);
        return $last_id;
    }

  public function clearLocks () {
    
  }

  public function lock() {
    
  }
  public function unlock() {
    
  }


  public function mkSQL() {
    return Queryi::mkSQLFromArray(func_get_args());
  }

  public function mkSQLFromArray($params) {
    $badSqlFmt = T("Bad mkSQL() format string.");

    $n = count($params);
    if ($n < 1) {
      Fatal::internalError(T("Not enough arguments given to mkSQL()."));
    }
    $i = 1;
    $SQL = "";
    $fmt = $params[0];
    while (strlen($fmt)) {
      $p = strpos($fmt, "%");
      if ($p === false) {
        $SQL .= $fmt;
        break;
      }
      $SQL .= substr($fmt, 0, $p);
      if (strlen($fmt) < $p+2) {
        Fatal::internalError($badSqlFmt);
      }
      if ($fmt[$p+1] == '%') {
        $SQL .= "%";
      } else {
        if ($i >= $n) {
          Fatal::internalError(T("Not enough arguments given to mkSQL()."));
        }
        $arg = $params[$i++];
        switch ($fmt[$p+1]) {
        case '!':

          $SQL .= $arg;
          break;
        case 'B':
          if ($arg) {
            $SQL .= "'Y'";
          } else {
            $SQL .= "'N'";
          }
          break;
        case 'C':
          $a = array();
          foreach (explode('.', $arg) as $ident) {
            array_push($a, '`'.$this->_ident($ident).'`');
          }
          $SQL .= implode('.', $a);
          break;
        case 'I':
          $SQL .= '`'.$this->_ident($arg).'`';
          break;
        case 'i':
          $SQL .= $this->_ident($arg);
          break;
        case 'N':
          $SQL .= $this->_numstr($arg);
          break;
        case 'Q':
          $SQL .= "'".addslashes($arg)."'";
          //$SQL .= "'".$arg."'";
          break;
        case 'q':
          $SQL .= addslashes($arg);
          //$SQL .=$arg;
          break;
        default:
          Fatal::internalError($badSqlFmt);
        }
      }
      $fmt = substr($fmt, $p+2);
    }
    if ($i != $n) {
      Fatal::internalError(T("Too many arguments to mkSQL()."));
    }
    return $SQL;
  }

  protected function _ident($i) {

    return str_replace('`', '', $i);
  }
  private function _numstr($n) {

      if (is_array($n)) {
          $n = reset($n); // get first element
      }


      $n = (string)$n;

      if (preg_match("/^([+-]?[0-9]+(\.[0-9]*)?([Ee][0-9]+)?)/", $n, $subs)) {
          return $subs[1];
      } else {
          return "0";
      }
  }
}
