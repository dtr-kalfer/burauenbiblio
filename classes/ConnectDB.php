<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
	 
class ConnectDB {
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
				$oldConfig = __DIR__ . '/../dbParams.php';

				if (file_exists($oldConfig)) {
						// Include the file in *this object’s scope* so `$this->dsn[...]` lines execute correctly
						include($oldConfig);

						// ✅ At this point, dbParams.php has already populated $this->dsn
						if (isset($this->dsn) && is_array($this->dsn)) {
								return; // done
						} else {
								die("Invalid database configuration in dbParams.php (dsn not set).");
						}
				} else {
						die("Missing database configuration: dbParams.php not found.");
				}
		}

    /**
     * Establish database connection
     */
    protected function connect() {
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
    protected function select($query, $types = "", $params = []) {
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
     * Execute a prepared CREATE/INSERT/UPDATE/DELETE query
     */
    protected function execute($query, $types = "", $params = []) {
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
	
		/**
     * Helper function
     */

		public function beginTransaction() {
        $this->connect()->begin_transaction();
    }

    public function commit() {
        $this->connect()->commit();
    }

    public function rollback() {
        $this->connect()->rollback();
    }
}
