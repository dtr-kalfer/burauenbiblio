<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * For status display of the BurauenBiblio App.
	 * it now uses prepared statements and built-in class functions.
	 * See the file COPYRIGHT.html for more details. --> F.Tumulak
	 */
	require_once("../shared/common.php");
	
	$tab = "admin";
	$nav = "info";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
	// This case only uses the ConnectDB class --F.Tumulak
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
			
				$stats = new ConnectDB();
				$server_stats = $stats->get_server_info();
				echo "Database Server Version: <b>" . $server_stats . "</b>";
				$stats->close();
			
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
	</section>	
	
	<?php
		 require_once(REL(__FILE__,'../shared/footer.php'));
	?>

<script>
$('#showThis').show();
</script>
</body>
</html>
