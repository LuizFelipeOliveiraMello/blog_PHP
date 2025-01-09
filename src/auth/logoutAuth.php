<?php
header('Content-Type: application/json');

session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_SESSION['user-id'])) {
        session_destroy();
        $_URL = 'http://localhost/blog_PHP-main/src/views/loginView.php';
        echo json_encode($_URL);
        exit;
    } else {
        $_URL = 'http://localhost/blog_PHP-main/src/views/loginView.php';
        echo json_encode($_URL);
        exit;
    }
}
?>
