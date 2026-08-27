<?php
    // Database credentials
    $host = 'localhost';
    $db = 'kenyali1_kenya_live_tv';
    $user = 'kenyali1_kenya_live_tv';
    $pass = 'kenyali1_kenya_live_tv';

    // Setup file path for logs
    $logFile = __DIR__ . '/connection_log.txt';

    // Set up the database connection
    if (!isset($mysqli)) {
        $mysqli = new mysqli($host, $user, $pass, $db);
        if ($mysqli->connect_error) {
            $errorMsg = 'Connection failed: ' . $mysqli->connect_error;
            file_put_contents($logFile, date("Y-m-d H:i:s") . " - MySQLi - $errorMsg - Called from: " . debug_backtrace()[0]['file'] . "\n", FILE_APPEND);
            die($errorMsg);
        } else {
            // file_put_contents($logFile, date("Y-m-d H:i:s") . " - MySQLi connection OK - Called from: " . debug_backtrace()[0]['file'] . "\n", FILE_APPEND);
        }
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // file_put_contents($logFile, date("Y-m-d H:i:s") . " - PDO connection OK - Called from: " . debug_backtrace()[0]['file'] . "\n", FILE_APPEND);

    } catch (PDOException $e) {
        $errorMsg = "PDO error: " . $e->getMessage();
        file_put_contents($logFile, date("Y-m-d H:i:s") . " - $errorMsg - Called from: " . debug_backtrace()[0]['file'] . "\n", FILE_APPEND);
    }

?>