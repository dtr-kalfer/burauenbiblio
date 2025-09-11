<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. -- F.Tumulak
	 */
	 
class Qtest {
    /** @var mysqli|null $connection - Database connection object */
    private $connection = null;

    /** @var array $dsn - Stores DB connection parameters from dbParams.php */
    private $dsn = [];

    /**
     * Load database credentials from dbParams2.php
		 * if dbParams2.php not exist, create a new copy from dbParams.php (original)
		 * and use the new format dbParams2.php -- F.Tumulak
     */
		private function loadDSN() {
				$newConfig = __DIR__ . '/../dbParams2.php';
				$oldConfig = __DIR__ . '/../dbParams.php';

				// ✅ If dbParams2.php already exists, just load it
				if (file_exists($newConfig)) {
						include($newConfig);
						if (isset($dsn) && is_array($dsn)) {
								$this->dsn = $dsn;
								return;
						} else {
								die("Invalid database configuration in dbParams2.php");
						}
				}

				// ⚠ If dbParams2.php does NOT exist, try to convert dbParams.php
				if (file_exists($oldConfig)) {
						// Temporarily create a separate scope to avoid `$this` issues
						$legacyDSN = [];
						include($oldConfig);

						// Check if $this->dsn exists in the old file
						if (isset($this->dsn) && is_array($this->dsn)) {
								$legacyDSN = $this->dsn;
						} elseif (isset($dsn) && is_array($dsn)) {
								// In case someone already updated the old file to use $dsn
								$legacyDSN = $dsn;
						} else {
								die("Invalid database configuration in dbParams.php");
						}

						// Generate the content for dbParams2.php
						$content = "<?php\n\$dsn = [\n";
						foreach ($legacyDSN as $key => $value) {
								$content .= "    \"$key\" => \"" . addslashes($value) . "\",\n";
						}
						$content .= "];\n";

						// Save dbParams2.php automatically
						if (file_put_contents($newConfig, $content) === false) {
								die("Failed to generate dbParams2.php. Please check folder permissions.");
						}

						// Load the newly created dbParams2.php
						include($newConfig);
						if (isset($dsn) && is_array($dsn)) {
								$this->dsn = $dsn;
						} else {
								die("Failed to load the newly created dbParams2.php");
						}
				} else {
						// ❌ No config file found at all
						die("Missing database configuration: Neither dbParams2.php nor dbParams.php found.");
				}
		}


    /**
     * Establish database connection
     */
    public function connect() {
        // Load credentials only if not already loaded
        if (empty($this->dsn)) {
            $this->loadDSN();
        }

        // Create a new connection, inherit new functions from mysqli_connect
        $this->connection = @mysqli_connect(
            $this->dsn['host'],
            $this->dsn['username'],
            $this->dsn['pwd'],
            $this->dsn['database']
        );

        if (mysqli_connect_errno()) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        return $this->connection;
    }

    /**
     * Execute a prepared SELECT query
     */
    public function select($query, $types = "", $params = []) {
        $conn = $this->connect();
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }

    /**
     * Execute a prepared INSERT/UPDATE/DELETE query
     */
    public function execute($query, $types = "", $params = []) {
        $conn = $this->connect(); // inherit new functions i.e. prepare, bind_param, execute.. etc
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();
        if (!$success) {
            die("Query execution failed: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }

    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            mysqli_close($this->connection);
            $this->connection = null;
        }
    }
}
