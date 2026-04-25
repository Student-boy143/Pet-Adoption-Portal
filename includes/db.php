<?php
// includes/db.php — PostgreSQL connection

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'fureverhome');
define('DB_USER', 'postgres');  
define('DB_PASS', '');  

//handles connection errors
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        DB_HOST, DB_PORT, DB_NAME
    );
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    die('Database connection failed. Check your credentials in includes/db.php');
}