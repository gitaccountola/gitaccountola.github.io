<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$db = new mysqli('localhost', 'mziqpyca_SimplePaste', '>dq:C"F$5kZpWh*', 'mziqpyca_SimplePaste');

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the content and password from the POST request
$content = $_POST['content'];
$password = $_POST['password'] ?? null; // Get password if provided

// Validate the content
if (empty($content)) {
    die("Content cannot be empty.");
}

// Generate a unique ID for the paste
$pasteId = uniqid();

// Hash the password using md5 (not secure)
$hashedPassword = $password ? md5($password) : null; // Correct usage of md5 function

// Prepare and execute the SQL statement
$stmt = $db->prepare("INSERT INTO pastes (id, content, password) VALUES (?, ?, ?)");
if (!$stmt) {
    die("Query preparation failed: " . $db->error);
}

$stmt->bind_param("sss", $pasteId, $content, $hashedPassword);

// Execute the statement and check for errors
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

// Return the paste ID
echo $pasteId;

// Close the statement and connection
$stmt->close();
$db->close();
?>