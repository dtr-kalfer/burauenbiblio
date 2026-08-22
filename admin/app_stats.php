<?php
    /* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
     * For status display of the BurauenBiblio App.
     * Universal version for WAMP (Windows) and LAMP (Linux).
     * See the file COPYRIGHT.html for more details. --> F.Tumulak
     */
    require_once("../shared/common.php");
    
    $tab = "admin";
    $nav = "info";
    require_once(REL(__FILE__, "../shared/logincheck.php"));
    Page::header(array('nav' => $tab . '/' . $nav, 'title' => ''));

    // Attempt autoload for ConnectDB, fallback to Qtest if necessary
    if (file_exists(__DIR__ . '/../autoload.php')) {
        require_once __DIR__ . '/../autoload.php';
    }
    if (!class_exists('ConnectDB') && file_exists("../catalog/class/Qtest.php")) {
        require_once("../catalog/class/Qtest.php");
    }
?>

    <!------------- ------------->
    <section id="showThis">    
        <div class="section">
            <h2>PHP Version</h2>
            <?php 
            preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $match);
            echo "PHP Version: <b>" . $match[0] . "</b>";
            ?>
        </div>

        <div class="section">
            <h2>Web Server</h2>
            <?php 
            if (isset($_SERVER['SERVER_SOFTWARE'])) {
                $server = preg_replace('/\s*\(.*?\)\s*/', ' ', $_SERVER['SERVER_SOFTWARE']);
                echo "Web Server: <b>" . htmlspecialchars(trim($server)) . "</b>";
            } else {
                echo "Web server information not available.";
            }
            ?>
        </div>

				<div class="section">
            <h2>Database Version</h2>
            <?php 
            if (class_exists('ConnectDB')) {
                $stats = new ConnectDB();

                // Check if get_server_info() exists as a method
                if (method_exists($stats, 'get_server_info')) {
                    $server_stats = $stats->get_server_info();
                // If ConnectDB extends mysqli directly, check property
                } elseif (isset($stats->server_info)) {
                    $server_stats = $stats->server_info;
                // If ConnectDB exposes a underlying connection object/link
                } elseif (method_exists($stats, 'get_link') && is_object($stats->get_link())) {
                    $server_stats = $stats->get_link()->server_info;
                } else {
                    $server_stats = "Version check method unavailable on ConnectDB.";
                }

                echo "Database Server Version: <b>" . htmlspecialchars($server_stats) . "</b>";

                if (method_exists($stats, 'close')) {
                    $stats->close();
                }
            } elseif (class_exists('Qtest')) {
                $mypass = new Qtest();
                $a_host = $mypass->getDSN2("host");
                $a_user = $mypass->getDSN2("username");
                $a_pwd  = $mypass->getDSN2("pwd");

                $mysqli = @new mysqli($a_host, $a_user, $a_pwd);
                if ($mysqli->connect_errno) {
                    echo "MySQL/MariaDB Connection Failed: " . htmlspecialchars($mysqli->connect_error);
                } else {
                    echo "Database Server Version: <b>" . htmlspecialchars($mysqli->server_info) . "</b>";
                    $mysqli->close();
                }
            } else {
                echo "Database connection class not found.";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>Disk Space</h2>
            <?php
            // Dynamically set root directory based on OS
            $targetDir = (stristr(PHP_OS, 'WIN')) ? __DIR__ : '/';

            $free = @disk_free_space($targetDir);
            $total = @disk_total_space($targetDir);

            if ($free !== false && $total !== false) {
                echo "Disk Free: <b>" . round($free / 1024 / 1024, 2) . " MB</b><br>";
                echo "Disk Total: <b>" . round($total / 1024 / 1024, 2) . " MB</b>";
            } else {
                echo "Disk space information unavailable.";
            }
            ?>
        </div>

        <div class="section">
            <h2>PHP Script Memory Usage</h2>
            <?php
            echo "Current Memory Usage: <b>" . round(memory_get_usage() / 1024, 2) . " KB</b><br>";
            echo "Peak Memory Usage: <b>" . round(memory_get_peak_usage() / 1024, 2) . " KB</b>";
            ?>
        </div>
        
        <div class="section">
            <h2>Date, Time, Burauenbiblio Timezone Settings</h2>
            <p><b><?php echo T("Today is: ") . date('Y-m-d H:i:s') . " " . date_default_timezone_get(); ?></b></p>
            <p>How to Set Timezone: Admin > Library Settings > Locale</p>
        </div>
        
    </section>    
    
    <?php
        require_once(REL(__FILE__, '../shared/footer.php'));
    ?>

<script>
$('#showThis').show();
</script>
</body>
</html>