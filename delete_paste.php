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

// Get the paste ID from the POST request
$pasteId = $_POST['id'] ?? null;

// Validate the paste ID
if (empty($pasteId) || !preg_match('/^[a-zA-Z0-9]+$/', $pasteId)) {
    die("Invalid paste ID.");
}

// Prepare and execute the SQL statement
$stmt = $db->prepare("DELETE FROM pastes WHERE id = ?");
if (!$stmt) {
    die("Query preparation failed: " . $db->error);
}

$stmt->bind_param("s", $pasteId);

// Execute the statement and check for errors
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

// Redirect back to a confirmation page or the main page
header("Location: index.html?message=Paste deleted successfully.");
exit;

// Close the statement and connection
$stmt->close();
$db->close();
?>