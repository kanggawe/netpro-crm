<?php
/**
 * Enterprise Multi-Database Connection Engine (PostgreSQL Multi-Server & SQLite Fallback)
 * 1. Primary App Database ($pdo): CRM, Dual Billing, Finance PSAK, HR, Inventory, NOC
 * 2. Dedicated FreeRADIUS Database ($pdoRadius): radcheck, radreply, radgroupreply, radacct, nas
 */

// Load .env if exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// 1. Primary App Database Connection Settings
$dbDriver = getenv('DB_DRIVER') ?: 'pgsql';
$dbHost   = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort   = getenv('DB_PORT') ?: '5432';
$dbName   = getenv('DB_NAME') ?: 'billdash';
$dbUser   = getenv('DB_USER') ?: 'postgres';
$dbPass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'postgres';

// 2. Dedicated FreeRADIUS Database Settings (Optional Multi-Server)
$radiusDbDriver = getenv('RADIUS_DB_DRIVER') ?: $dbDriver;
$radiusDbHost   = getenv('RADIUS_DB_HOST') ?: $dbHost;
$radiusDbPort   = getenv('RADIUS_DB_PORT') ?: $dbPort;
$radiusDbName   = getenv('RADIUS_DB_NAME') ?: $dbName;
$radiusDbUser   = getenv('RADIUS_DB_USER') ?: $dbUser;
$radiusDbPass   = getenv('RADIUS_DB_PASS') !== false ? getenv('RADIUS_DB_PASS') : $dbPass;

$pdo = null;
$pdoRadius = null;
$activeDriver = 'pgsql';

// --- INITIATE PRIMARY APP DATABASE ---
if ($dbDriver === 'pgsql') {
    try {
        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName};";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3
        ]);
        $activeDriver = 'pgsql';
        $pdo->exec("SET timezone TO 'Asia/Jakarta';");

        // Auto-create PostgreSQL schema if tables do not exist yet
        $checkTable = $pdo->query("SELECT to_regclass('public.customers')")->fetchColumn();
        if (!$checkTable) {
            $schemaFile = __DIR__ . '/../database/postgresql_schema.sql';
            if (file_exists($schemaFile)) {
                $pdo->exec(file_get_contents($schemaFile));
            }
        }
    } catch (Throwable $e) {
        // Fallback to local SQLite for development
        $sqliteFile = __DIR__ . '/../database/app.db';
        try {
            $pdo = new PDO("sqlite:" . $sqliteFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $activeDriver = 'sqlite';
        } catch (Throwable $sqle) {
            die("Database Connection Error (PostgreSQL & SQLite Fallback Failed): " . $e->getMessage());
        }
    }
} else {
    $sqliteFile = __DIR__ . '/../database/app.db';
    $pdo = new PDO("sqlite:" . $sqliteFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $activeDriver = 'sqlite';
}

// --- INITIATE DEDICATED FREERADIUS DATABASE ---
if ($radiusDbHost !== $dbHost || $radiusDbName !== $dbName) {
    try {
        $dsnRadius = "pgsql:host={$radiusDbHost};port={$radiusDbPort};dbname={$radiusDbName};";
        $pdoRadius = new PDO($dsnRadius, $radiusDbUser, $radiusDbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3
        ]);
        $pdoRadius->exec("SET timezone TO 'Asia/Jakarta';");

        // Auto-create FreeRADIUS official schema if not exists
        $checkRadTable = $pdoRadius->query("SELECT to_regclass('public.radcheck')")->fetchColumn();
        if (!$checkRadTable) {
            $radSchemaFile = __DIR__ . '/../database/radius_database_schema.sql';
            if (file_exists($radSchemaFile)) {
                $pdoRadius->exec(file_get_contents($radSchemaFile));
            }
        }
    } catch (Throwable $e) {
        // Graceful fallback to primary PDO if radius database is unavailable
        $pdoRadius = $pdo;
    }
} else {
    // Single Database Deployment (Same DB instance)
    $pdoRadius = $pdo;
}

// Global Connection Accessors
function get_db() {
    global $pdo;
    return $pdo;
}

function get_radius_db() {
    global $pdoRadius, $pdo;
    return $pdoRadius ?? $pdo;
}

// Auto-migration columns check on Primary DB
try { $pdo->exec("ALTER TABLE customers ADD COLUMN pppoe_user VARCHAR(100)"); } catch (Throwable $e) {}
try { $pdo->exec("ALTER TABLE customers ADD COLUMN pppoe_password VARCHAR(100)"); } catch (Throwable $e) {}
try { $pdo->exec("ALTER TABLE customers ADD COLUMN auth_method VARCHAR(20) DEFAULT 'pppoe'"); } catch (Throwable $e) {}
