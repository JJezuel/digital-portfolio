<?php
// Respond with JSON
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

ini_set('display_errors', 0);
error_reporting(E_ALL);

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. ']);
    exit;
}

// Load DB credentials
include 'config.php';

// Sanitise input
$name      = htmlspecialchars(trim($_POST['name']      ?? ''), ENT_QUOTES, 'UTF-8');
$email     = htmlspecialchars(trim($_POST['email']     ?? ''), ENT_QUOTES, 'UTF-8');
$category  = htmlspecialchars(trim($_POST['category']  ?? ''), ENT_QUOTES, 'UTF-8');
$challenge = htmlspecialchars(trim($_POST['challenge'] ?? ''), ENT_QUOTES, 'UTF-8');

// Server-side validation
$allowed = ['sales', 'ai', 'consulting', 'hiring', 'other'];
$errors = [];

if (strlen($name) < 2)                             $errors[] = 'Name is too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors[] = 'Invalid email address.';
if (!in_array($category, $allowed, true))          $errors[] = 'Invalid category.';
if (strlen($challenge) < 10)                       $errors[] = 'Message is too short.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Connect & insert
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log('DB connect error: ' . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error — please try again later.']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO submissions (name, email, category, challenge) VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error — please try again later.']);
    $conn->close();
    exit;
}

$stmt->bind_param('ssss', $name, $email, $category, $challenge);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => "Thanks {$name} — I've received your message and will be in touch soon."
    ]);
} else {
    error_log('Execute error: ' . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error — please try again later.']);
}

$stmt->close();
$conn->close();
?>