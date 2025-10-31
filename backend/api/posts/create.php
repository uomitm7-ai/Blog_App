<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$content = $data['content'] ?? '';

if (!$title || !$content) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and content required']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $content]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post']);
}
?>