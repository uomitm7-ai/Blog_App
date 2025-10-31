

<?php
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireLogin();

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid post ID']);
    exit();
}

// Fetch current post 
$stmt = $pdo->prepare("SELECT user_id, image FROM blogPost WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found']);
    exit();
}

if (!isOwner($post['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// handle image upload
$imageFilename = $post['image'];
if (!empty($_FILES['image']['name'])) {
    $targetDir = __DIR__ . '/../../../uploads/';
    $fileName = basename($_FILES['image']['name']);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileType = strtolower($fileType);

    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Only JPG, PNG, or WebP images allowed.']);
        exit();
    }

    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'Image must be less than 2MB.']);
        exit();
    }

    // Delete old image
    if ($post['image'] && file_exists($targetDir . $post['image'])) {
        unlink($targetDir . $post['image']);
    }

    $uniqueName = uniqid() . '_' . time() . '.' . $fileType;
    $targetFile = $targetDir . $uniqueName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

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
    echo json_encode(['error' => 'Title and content required']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $content, $imageFilename, $id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update post']);
}
?>