<?php
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit();
}

$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM blogPost p 
    JOIN user u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found']);
    exit();
}

echo json_encode($post);
?>