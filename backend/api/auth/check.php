<?php
require_once '../../config/db.php';
if (isset($_SESSION['user_id'])) {
    echo json_encode(['user' => [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ]]);
} else {
    echo json_encode(['user' => null]);
}
?>