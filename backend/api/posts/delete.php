<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID required']);
    exit();
}

$stmt = $pdo->prepare("SELECT user_id FROM blogPost WHERE id = ?");
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

$stmt = $pdo->prepare("DELETE FROM blogPost WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['success' => true]);
?>