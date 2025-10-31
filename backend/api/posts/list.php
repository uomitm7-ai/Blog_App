

<?php
require_once '../../config/db.php';

$stmt = $pdo->query("
    SELECT p.id, p.title, p.content, p.image, p.created_at, u.username 
    FROM blogPost p 
    JOIN user u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");

$posts = [];
while ($row = $stmt->fetch()) {
    $posts[] = $row;
}
echo json_encode($posts);
?>