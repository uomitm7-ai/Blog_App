<?php
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireLogin();

// handle file uploads
$imageFilename = null;
if (!empty($_FILES['image']['name'])) {
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/blog-app/uploads/';
    $fileName = basename($_FILES['image']['name']);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileType = strtolower($fileType);

    // validate file  type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Only JPG, PNG, or WebP images allowed.']);
        exit();
    }

    // validate file size (2MB max)
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'image must be less than 2MB.']);
        exit();
    }

    // generate unique filename to avoid conflicts
    $uniqueName = uniqid() . '_' . time() . '.' . $fileType;
    $targetFile = $targetDir . $uniqueName;

    // Create uploads dir if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Move Uploaded file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image.']);
        exit();
    }
    $imageFilename = $uniqueName;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if (!$title || !$content) {
    http_response_code(400);
    echo json_encode(['error' => 'title and content required']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $content, $imageFilename]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post']);
}
?>