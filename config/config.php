<?php
    // Start session
    session_start();

    // Set default timezone
    date_default_timezone_set('UTC');

    // Define constants
    include __DIR__ . '/constants.php';

    // Include the database connection
    include __DIR__ .'/db.php';
?>