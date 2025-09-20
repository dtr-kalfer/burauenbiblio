<?php
/*
 * BurauenBiblio Migration Class
 * -----------------------------
 * Upgrades an old OpenBiblio database schema to make it compatible with BurauenBiblio.
 *
 * This script applies ALTER TABLE and CREATE TABLE commands safely.
 * Each migration is executed only once and logged in `schema_migrations`.
 *
 * ALWAYS BACK UP YOUR DATABASE BEFORE RUNNING THIS SCRIPT.
 *
 * -- Improved by ChatGPT & F. Tumulak
 *
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
namespace Migration;

class MigrationManager extends \ConnectDB
{
    /** @var array List of migrations */
    private $migrations = [];

    public function __construct(string $migrationFile = "../migrations.php")
    {
        if (!file_exists($migrationFile)) {
            die("Migration file not found: " . htmlspecialchars($migrationFile));
        }

        $data = include $migrationFile;

        if (!is_array($data)) {
            die("Migration file format error: expected an array of migrations.");
        }

        $this->migrations = $data;
    }

    private function ensureMigrationsTable()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function getAppliedMigrations(): array
    {
        $rows = $this->select("SELECT migration_name FROM schema_migrations");
        return array_column($rows, "migration_name");
    }

    public function runMigrations()
    {
        $this->ensureMigrationsTable();
        $applied = $this->getAppliedMigrations();
        $appliedCount = 0;

foreach ($this->migrations as $migration) {
    if (!isset($migration['name'], $migration['sql'])) {
        echo "<p style='color:red;'>⚠️ Invalid migration entry (missing 'name' or 'sql'). Skipping...</p>";
        continue;
    }

    if (!in_array($migration['name'], $applied)) {
        echo "<p>Applying migration: <strong>{$migration['name']}</strong>...</p>";

        try {
            // Each migration gets its own transaction
            $this->beginTransaction();

            $this->execute($migration['sql']);
            $this->execute(
                "INSERT INTO schema_migrations (migration_name) VALUES (?)",
                "s",
                [$migration['name']]
            );

            $this->commit();
            $appliedCount++;
            echo "<p style='color:green;'>✅ Migration '{$migration['name']}' applied successfully.</p>";
        } catch (\Exception $e) {
            $this->rollback();
            echo "<p style='color:red;'>❌ Failed migration '{$migration['name']}': {$e->getMessage()}</p>";
            
            // ✅ Continue with next migration
            continue;
        }
    } else {
        echo "<p style='color:blue;'>⏩ Skipping already applied migration: {$migration['name']}</p>";
    }
}


        if ($appliedCount > 0) {
            echo "<p style='color:green; font-weight:bold;'>🎉 $appliedCount new migration(s) applied successfully!</p>";
        } else {
            echo "<p style='color:blue; font-weight:bold;'>✅ Database schema is already up-to-date.</p>";
        }

        $this->close();
    }
}
