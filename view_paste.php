<?php
session_start(); // Start the session to track attempts

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$db = new mysqli('localhost', 'mziqpyca_SimplePaste', '>dq:C"F$5kZpWh*', 'mziqpyca_SimplePaste');

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the paste ID from the URL and trim whitespace
$pasteId = trim($_GET['id']);

// Validate the paste ID
if (empty($pasteId) || !preg_match('/^[a-zA-Z0-9]+$/', $pasteId)) {
    die("Invalid paste ID."); // This message can be customized
}

// Prepare and execute the SQL statement
$stmt = $db->prepare("SELECT content, password FROM pastes WHERE id = ?");
if (!$stmt) {
    die("Query preparation failed: " . $db->error);
}

$stmt->bind_param("s", $pasteId);
$stmt->execute();
$result = $stmt->get_result();
$paste = $result->fetch_assoc();

// Check if the paste exists
if ($paste) {
    // Check for password
    if ($paste['password']) {
        // Initialize attempts if not set
        if (!isset($_SESSION['attempts'])) {
            $_SESSION['attempts'] = 0;
        }

        // Prompt for password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check the submitted password
            if (password_verify($_POST['password'], $paste['password'])) {
                // Password is correct, display content
                displayPaste($paste['content']);
                // Reset attempts on successful login
                unset($_SESSION['attempts']);
            } else {
                // Increment attempts
                $_SESSION['attempts']++;

                // Password is incorrect, show the password form again with an error message
                showPasswordForm("Incorrect password.");
            }
        } else {
            // Show the password form
            showPasswordForm();
        }
    } else {
        // If no password, display content
        displayPaste($paste['content']);
    }
} else {
    echo "Paste not found.";
}

// Close the statement and connection
$stmt->close();
$db->close();

// Function to display the paste content
function displayPaste($content) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>View Paste</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background-color: white; /* Default background color */
                color: black; /* Default text color */
                transition: background-color 0.3s, color 0.3s; /* Smooth transition */
            }
            pre {
                white-space: pre-wrap;
                word-wrap: break-word;
                background-color: #f4f4f4;
                padding: 15px;
                border-radius: 5px;
            }
            .dark-mode {
                background-color: black; /* Dark background */
                color: white; /* Light text */
            }
            .dark-mode pre {
                background-color: #333; /* Darker background for preformatted text */
                color: white; /* Light text for preformatted text */
            }
            .error {
                color: red;
                font-size: 1.2em; /* Increase font size */
                margin-bottom: 10px;
            }
            #themeToggle {
                background: none;
                border: none;
                cursor: pointer;
                padding: 10px; /* Adjust padding for smaller button */
                transition: transform 0.3s; /* Smooth transition for scaling */
            }
            #themeToggle img {
                width: 30px; /* Set image size */
                height: 30px; /* Set image size */
            }
        </style>
    </head>
    <body>
        <h1>Paste Content</h1>
        <button id='themeToggle'><img id='themeIcon' src='images/sun.png' alt='Toggle Theme'></button>
        <pre>" . htmlspecialchars($content) . "</pre>
        <p><a href='https://paste.cyberlinux.x10.mx/' style='color: blue;'>Go back to SimplePaste</a></p>
        <script>
            // Check for user's color scheme preference
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Apply dark mode if the user prefers it
            if (prefersDarkScheme) {
                document.body.classList.add('dark-mode');
                document.getElementById('themeIcon').src = 'images/moon.png'; // Set icon to moon
            }

            const themeToggle = document.getElementById('themeToggle');
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const themeIcon = document.getElementById('themeIcon');
                if (document.body.classList.contains('dark-mode')) {
                    themeIcon.src = 'images/moon.png'; // Change to moon icon
                } else {
                    themeIcon.src = 'images/sun.png'; // Change to sun icon
                }
            });
        </script>
    </body>
    </html>";
}

// Function to show the password form
function showPasswordForm($errorMessage = "") {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Enter Password</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background-color: white; /* Default background color */
                color: black; /* Default text color */
            }
            .error {
                color: red;
                font-size: 1.2em; /* Increase font size */
                margin-bottom: 10px;
            }
            .dark-mode {
                background-color: black; /* Dark background */
                color: white; /* Light text */
            }
            #themeToggle {
                background: none;
                border: none;
                cursor: pointer;
                padding: 5px; /* Adjust padding for smaller button */
                transition: transform 0.3s; /* Smooth transition for scaling */
            }
            #themeToggle img {
                width: 20px; /* Set smaller image size */
                height: 20px; /* Set smaller image size */
            }
        </style>
    </head>
    <body>
        <h1>Enter Password</h1>"; // Removed "(You have 3 attempts)"
    if ($errorMessage) {
        echo "<div class='error'>" . htmlspecialchars($errorMessage) . "</div>"; // Display error message
    }
    echo "<form method='POST'>
            <label for='password'>Password:</label>
            <input type='password' name='password' required>
            <button type='submit'>Submit</button>
          </form>
          <button id='themeToggle'><img id='themeIcon' src='images/sun.png' alt='Toggle Theme'></button>
          <script>
              // Check for user's color scheme preference
              const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

              // Apply dark mode if the user prefers it
              if (prefersDarkScheme) {
                  document.body.classList.add('dark-mode');
                  document.getElementById('themeIcon').src = 'images/moon.png'; // Set icon to moon
              }

              const themeToggle = document.getElementById('themeToggle');
              themeToggle.addEventListener('click', function() {
                  document.body.classList.toggle('dark-mode');
                  const themeIcon = document.getElementById('themeIcon');
                  if (document.body.classList.contains('dark-mode')) {
                      themeIcon.src = 'images/moon.png'; // Change to moon icon
                  } else {
                      themeIcon.src = 'images/sun.png'; // Change to sun icon
                  }
              });
          </script>
    </body>
    </html>";
}
?>