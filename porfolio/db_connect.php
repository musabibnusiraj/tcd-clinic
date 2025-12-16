<?php
$host = 'localhost'; // Database host
$dbname = 'tooth_care'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password
$success = false;
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status = "Database connected successfully!";
    $success = true;
} catch (PDOException $e) {
    // If connection fails, display the error
    $status = "Database connection failed: " . $e->getMessage();
}
