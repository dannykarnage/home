<?php
    $dbhost = getenv('DB_HOST');
    $dbuser = getenv('PPT_DB_USER');
    $dbpass = getenv('PPT_DB_PASS');
    $dbname = getenv('PPT_DB_NAME');

    // Check if credentials were loaded
    if (!$dbhost || !$dbuser || !$dbpass || !$dbname) {
        error_log("FATAL ERROR: One or more database environment variables are missing.");
        die("Configuration error: Database credentials not found.");
    }

    // Initialize $conn using Object-Oriented mysqli
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        // Log the error detail internally, but only show a generic message to the user
        error_log("Connection failed: " . $conn->connect_error);
        die("Database connection failed. Please try again later.");
    }

    // Set character set to UTF-8 for security and compatibility
    $conn->set_charset("utf8mb4");
?>
