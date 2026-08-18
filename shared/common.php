<?php

declare(strict_types=1);

/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  define('DEV_MODE', true);

  if (DEV_MODE) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('track_errors', '1');
    error_reporting(E_ALL);
  } else {
    error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT | E_NOTICE));
    ini_set('display_errors', '0');
  }
  # ----------------------------------------------------

  # Forcibly disable register_globals (legacy safety — PHP 8.3 ignores this directive)
  if (ini_get('register_globals')) {
    foreach ($_REQUEST as $k => $v) {
      unset(${$k});
    }
    foreach ($_ENV as $k => $v) {
      unset(${$k});
    }
    foreach ($_SERVER as $k => $v) {
      unset(${$k});
    }
  }

  # assert_options() was removed in PHP 8.0.
  # In PHP 8+, assert() uses zend.assertions INI directive.
  # We set up a compatibility shim for the old obAssertHandler.
  if (!function_exists('obAssertHandler')) {
    function obAssertHandler(string $file, int $line, ?string $code = null, ?string $desc = null): void {
      echo "Assertion failed at file:'{$file}', line:'{$line}', code:'{$code}'";
      if ($desc) echo ": {$desc}";
      echo "<br/>\n";
    }
  }

  #apd_set_pprof_trace();
  if (isset($cache)) {
    session_cache_limiter($cache);
  } else {
    session_cache_limiter('nocache');
  }

  function getOBroot(): string {
    // obtain OpenBiblio path ref to web pages root
    // may be useful later in system (thinking plug-ins, etc.)
    $thisApp = $_SERVER['PHP_SELF'];
    $thisPath = pathinfo($thisApp, PATHINFO_DIRNAME);
    $pathParts = explode('/', $thisPath);
    $OBroot = '/' . ($pathParts[1] ?? '') . '/';
    return $OBroot;
  }

  /* Convenience functions for everywhere */
  /* Work around PHP's braindead include_path stuff. - MS */
  function REL(string $sf, string $if): string {
    return dirname($sf) . '/' . $if;
  }

  ### needs to be here so changes in settings are picked up when changes are entered
  function setSessionFmSettings(): void {
    $_SESSION['itemBarcode_flg'] = Settings::get('item_barcode_flg');
    $_SESSION['item_autoBarcode_flg'] = Settings::get('item_autoBarcode_flg');
    $_SESSION['item_barcode_width'] = Settings::get('item_barcode_width');
    $_SESSION['mbrBarcode_flg'] = Settings::get('mbr_barcode_flg');
    $_SESSION['mbr_autoBarcode_flg'] = Settings::get('mbr_autoBarcode_flg');
    $_SESSION['allow_plugins_flg'] = Settings::get('allow_plugins_flg');
    $_SESSION['plugin_list'] = Settings::get('plugin_list');
    $_SESSION['show_checkout_mbr'] = Settings::get('show_checkout_mbr');
    $_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');
    $_SESSION['multi_site_func'] = Settings::get('multi_site_func');
    $_SESSION['show_item_photos'] = Settings::get('show_item_photos');
    $_SESSION['site_login'] = Settings::get('site_login');
    $_SESSION['checkout_interval'] = Settings::get('checkout_interval');
  }

  require_once(REL(__FILE__, '../shared/global_constants.php'));
  require_once(REL(__FILE__, '../classes/Error.php'));
  require_once(REL(__FILE__, '../classes/Nav.php'));
  require_once(REL(__FILE__, '../classes/Localize.php'));
  require_once(REL(__FILE__, '../shared/templates.php'));
  require_once(REL(__FILE__, '../functions/supportFuncs.php'));
  require_once(REL(__FILE__, '../model/Settings.php'));

  global $LOC, $CharSet, $Locale, $OBroot;
  global $ThemeId, $ThemeDirUrl, $ThemeDir, $SharedDirUrl;
  global $LocaleDirUrl, $LocaleDir, $HTMLHead;

  $LOC = new Localize;
  if (!isset($doing_install) || !$doing_install) {
    ## normal processing
    include_once(REL(__FILE__, '../model/Settings.php'));
    Settings::load();
    $tz = Settings::get('timezone');
    if (!empty($tz)) {
      date_default_timezone_set($tz);
    }
    $CharSet = Settings::get('charset');
    $ThemeId = Settings::get('themeid');
    $ThemeDirUrl = trim(Settings::get('theme_dir_url'));
    $Locale = Settings::get('locale');
  } else {
    ## startup / install only
    $CharSet = 'UTF-8';
    $ThemeId = '1';
    $ThemeDirUrl = '../themes/default';
    $Locale = 'en';
  }

  $ThemeDir = REL(__FILE__, $ThemeDirUrl);
  $SharedDirUrl = '../shared';
  $HTMLHead = '';
  $LocaleDirUrl = '../locale/' . $Locale;
  $LocaleDir = REL(__FILE__, $LocaleDirUrl);

  if (!isset($doing_install) || !$doing_install) {
    ## Change the session timeout value to 60 minutes (60 * 60 = 3600 seconds)
    ini_set('session.gc_maxlifetime', (string)(60 * 60));

    session_start();
    # Forcibly disable register_globals if php.ini does not do it already
    if (ini_get('register_globals')) {
      foreach ($_SESSION as $k => $v) {
        unset(${$k});
      }
    }

    setSessionFmSettings();
  }

  /* determine if OB code has changed - do not use this during install, data not present yet */
  if (!isset($doing_install) || !$doing_install) {
    $prevHash = Settings::get('version_hash');
    $allowCk = Settings::get('allow_auto_db_check');
    [$crntHash, $crntSize] = getOBVersionHash();
    if (($crntHash != $prevHash) && (str_contains($_SERVER['PHP_SELF'], 'dbChkrForms.php') === false) && ($allowCk == 'Y')) {
      header('Location: ../admin/dbChkrForms.php?tab=auto&rtnTo=' . $_SERVER['PHP_SELF']);
    }
    Settings::set('version_hash', $crntHash);
    Settings::set('OBsize', $crntSize);
  }

  $LOC->init($Locale);

  // ***********************************************
  // Here is where we construct the actual web page
  include_once(REL(__FILE__, '../classes/Page.php'));
  // ***********************************************

  ###################################################################
  ## plugin Support
  ###################################################################
  function getPlugIns(string $wanted): array {
    ## determine what is allowed
    if (($_SESSION['allow_plugins_flg'] ?? 'N') !== 'Y') {
      return [];
    }
    $list = $_SESSION['plugin_list'];
    $aray = explode(',', $list);

    ## make connections where allowed
    clearstatcache();
    $pluginSet = [];
    $plugDir = '../plugins';
    if (is_dir($plugDir)) {
      ## find all plugin directories
      $dirSet = scandir($plugDir);
      foreach ($dirSet as $plug) {
        # look at all plugin dirs
        if (in_array($plug, ['.', '..'], true)) continue;
        $plugPath = "../plugins/{$plug}";
        if (is_dir($plugPath)) {
          if (!in_array($plug, $aray)) continue; // not allowed

          $filSet = scandir($plugPath);
          foreach ($filSet as $file) {
            if (($file == '.') || ($file == '..')) continue;
            if ($file == $wanted) {
              $pluginSet[] = "{$plugPath}/{$file}";
            }
          }
        }
      }
    }
    return $pluginSet;
  }

  // Deprecated below, use the template-based functions - MS
  function H(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
  }
  function U(string $s): string {
    return urlencode($s);
  }
  function HURL(string $s): string {
    return H(U($s));
  }
  function JS(string $s): string {
    $r = '';
    $l = strlen($s);
    $subs = [
      '<' => '\\u003c',
      '>' => '\\u003e',
      '&' => '\\u0026',
      '\'' => '\\u0027',
      '"' => '\\u0022',
      '\\' => '\\\\',
      "\n" => '\\n',
      "\r" => '\\r',
    ];
    for ($i = 0; $i < $l; $i++) {
      if (isset($subs[$s[$i]])) {
        $r .= $subs[$s[$i]];
      } elseif (ord($s[$i]) < 32) {
        $r .= sprintf("\\u%04x", ord($s[$i]));
      } else {
        $r .= $s[$i];
      }
    }
    return $r;
  }
  function nT(string $n, string $s, $v = null): string {
    return T($s, $v);
  }