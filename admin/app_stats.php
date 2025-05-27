<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --> F.Tumulak
	 */
	require_once("../shared/common.php");
	require_once("../catalog/class/Qtest.php"); 
	$tab = "admin";
	$nav = "info";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

	<!------------- ------------->
	<div id="showThis">	
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
			$mypass = new Qtest;
			$a_host = $mypass->getDSN2("host");
			$a_user = $mypass->getDSN2("username");
			$a_pwd = $mypass->getDSN2("pwd");
			$a_db = $mypass->getDSN2("database");
		
			$mysqli = @new mysqli($a_host, $a_user, $a_pwd);
			if ($mysqli->connect_errno) {
					echo "MySQL/MariaDB Connection Failed: " . $mysqli->connect_error;
			} else {
					echo "Database Server Version: <b>" . $mysqli->server_info . "</b>";
					$mysqli->close();
			}
			?>
		</div>
		
		<div class="section">
			<h2>Disk Space</h2>
			<?php
			$free = disk_free_space("/");
			$total = disk_total_space("/");
			echo "Disk Free: <b>" . round($free / 1024 / 1024, 2) . " MB</b><br>";
			echo "Disk Total: <b>" . round($total / 1024 / 1024, 2) . " MB</b>";
			?>
		</div>

		<div class="section">
			<h2>PHP Script Memory Usage</h2>
			<?php
			echo "Current Memory Usage: <b>" . round(memory_get_usage() / 1024, 2) . " KB</b><br>";
			echo "Peak Memory Usage: <b>" . round(memory_get_peak_usage() / 1024, 2) . " KB</b>";
			?>
		</div>
	</div>	
	
	<?php
		 require_once(REL(__FILE__,'../shared/footer.php'));
	?>

<script>
$('#showThis').show();
</script>
</body>
</html>
