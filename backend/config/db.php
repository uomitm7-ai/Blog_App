<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost'); 
header('Access-Control-Allow-Credentials: true');

$host = 'localhost';
$dbname = 'blog_db';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}
?>