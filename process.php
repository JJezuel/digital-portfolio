<?php
include 'config.php';

// Connect
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs to prevent basic scripts from being injected
    $name      = htmlspecialchars($_POST['name']);
    $email     = htmlspecialchars($_POST['email']);
    $category  = htmlspecialchars($_POST['category']);
    $challenge = htmlspecialchars($_POST['challenge']);

    // Insert into Database (Table name: contacts)
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, category, challenge) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $category, $challenge);

    if ($stmt->execute()) {
        // Redirect back or show success
        echo "<h1>Success!</h1><p>Thank you, $name. I will be in touch soon.</p>";
        echo '<a href="index.html">Return to website</a>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>