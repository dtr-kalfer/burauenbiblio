<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Settings_refactored.php — PHP 8.3 compatible version with bug fixes.
 * To use: rename to Settings.php after testing.
 *
 * Changes from original:
 *   - array() → [] syntax throughout
 *   - FIXED: _mkField() undefined array key when setting has no validators
 *     (in_array('required', $_settings_validators[$s['name']])) → guarded with ?? []
 */

require_once(REL(__FILE__, '../classes/Queryi.php'));
require_once(REL(__FILE__, '../model/Sites.php'));

global $_settings_cache, $_settings_validators;
$_settings_cache = [];
$_settings_validators = [];

/* To be used statically. */
//class Settings extends Queryi
class Settings extends DBTable {
  public function __construct() {
    parent::__construct();
    $this->setName('settings');
    $this->setFields([
      'name'=>'string',
            'position'=>'number',
            'title'=>'string',
            'type'=>'string',
            'width'=>'number',
            'type_data'=>'string',
            'validator'=>'string',
            'value'=>'string',
            'menu'=>'string',
    ]);
        $this->setReq([
            'name', 'type', 'menu',
        ]);
    $this->setKey('name');
  }

  static public function load() {
    global $_settings_cache, $_settings_validators;
        //echo "in Settings::load() <br />\n";
    $db = new Queryi;
        $stmt = $db->act('SELECT * FROM settings');
        foreach ($stmt as $s) {
      $_settings_cache[$s['name']] = $s['value'];
      $_settings_validators[$s['name']] = explode(',', $s['validator']);
    }
        //echo "in Settings::load(), at end <br />\n";
  }
  static public function get($name) {
    global $_settings_cache;
    $_settings_cache[$name] = $_settings_cache[$name] ?? '';
    return $_settings_cache[$name];
  }
  static public function set($name, $value) {
    global $_settings_cache;
        $_settings_cache[$name] = $value;
        self::setOne_e($name, $value);
    return $_settings_cache[$name];
  }
  static public function getThemeDirs () {
    return Settings::_getSubdirs('themes');
  }
  static public function getFormFields($menu=NULL) {
    $r = Settings::_getData($menu);
    $fields = [];
        foreach ($r as $s) {
      $fields[] = Settings::_mkField($s);
    }
    return $fields;
  }
  static private function _getSubdirs($root) {
    $aray = [];
      if (is_dir('../'.$root)) {
      //echo $root." Dir found: <br />";
          ## find all sub-directories
      if ($dirHndl = opendir('../'.$root)) {
          # look at all sub-dirs
          while (false !== ($subdir = readdir($dirHndl))) {
              if (($subdir == '.') || ($subdir == '..')) continue;
          //echo "subdir => $subdir<br />";
              $path = "../".$root."/".$subdir;
                if (is_dir($path)) {
                  if (!in_array($path, $aray)) {
                    $aray[$path] = $path;
            }
          }
            }
            closedir($dirHndl);
      }
    }
    return $aray;
  }
  static private function _getData ($menu=NULL, $cols='*'){
    $db = new Queryi;
    $sql = "SELECT ".$cols." FROM settings WHERE (title <> '') ";
    if (!empty($menu)) {
      $sql .= " AND (menu = '$menu') ";
    }
    $sql .= " ORDER BY position ";
    //echo "sql={$sql}<br />\n";
    return $db->select($sql);
  }

    static public function getSettings() {
    global $_settings_cache;
    return $_settings_cache;
  }

  function getFormData ($menu=NULL, $cols = '*') {
    $r = $this->_getData($menu, $cols);
    $fields = [];
    //while ($s = $r->fetch_assoc()) {
        foreach ($r as $s) {
        $fields[] = $s;
    }
    return $fields;
  }
  static function setOne_e($name, $value) {
    # FIXME - VALIDATE
    $db = new Queryi;
    $db->lock();
    $sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $value, $name);
    $db->act($sql);
    $db->unlock();
    return NULL;
  }
  function setOne_el($name, $value) {
    # FIXME - VALIDATE
    $db = new Queryi;
    $db->lock();
    $sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $value, $name);
    $db->act($sql);
    $db->unlock();
    return 'success';
  }
  function setAll_el($settings) {
    $errors = [];
    # FIXME - VALIDATE
    if (!empty($errors)) {
      return $errors;
    }
    $db = new Queryi;
    $db->lock();
    foreach ($settings as $n=>$v) {
      $sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $v, $n);
      //echo "sql={$sql}<br />\n";
      $rslt = $db->act($sql);
//      $errors[] = $rslt->fetch();
    }
    $db->unlock();
    return;
  }
  static private function _mkField($s) {
    global $_settings_validators;
    $attrs = [];

    if ($s['width']) {
      $attrs['size'] = $s['width'];
    }

    if ($s['type'] == 'int') {
      $s['type'] = 'number';
    }

    $options = [];
    if ($s['type'] == 'select') {
      switch ($s['type_data']) {
      case 'locales':
        $options = Localize::getLocales();
        break;
      case 'sites':
        $sites = new Sites;
        $options = $sites->getSelect();
        break;
      case 'themes':
        $crntTheme = Settings::get('theme_dir_url');
        //echo "crnt theme= ".$crntTheme;
        $options = Settings::_getSubdirs('themes');
        $s['value'] = $crntTheme;
        break;
      case 'default':
        Fatal::internalError("Unknown select type in settings");
      }
      //if ($s['name'] == 'library_name') {
      //  $sites = new Sites;
      //  $options = $sites->getSelect();
      //}
      if ($s['name'] == 'checkout_interval') {
        $options = ['Days'];
      }
    }

    $label = '';
    if ($s['type'] != 'select' and $s['type_data'] !== NULL) {
      $label = $s['type_data'];
    }

    $required=false;
    // 🐛 FIXED: guard against undefined array key when setting has no validators
    if (in_array('required', $_settings_validators[$s['name']] ?? [])) {
      $required=true;
    }
    return [
      'name'=>$s['name'],
      'title'=>$s['title'],
      'type'=>$s['type'],
      'default'=>$s['value'],
      'attrs'=>$attrs,
      'options'=>$options,
      'required'=>$required,
      'label'=>$label,
    ];
  }
}