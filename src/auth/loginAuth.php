<?php
header('Content-Type: application/json');
include "../connectionArticles.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_email = $_POST['email'];
    $_password = $_POST['senha'];

    try {
        $db = new Database('../mydatabase.db');

        // Prepare the SELECT query
        $query = "SELECT * FROM users WHERE email = :email";
        $params = array('email' => $_email);
        $results = $db->select($query, $params);

        // Fetch the user
        $user = $results->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($_password, $user['senha'])) {
            session_start();
            $_SESSION['user-id'] = $user['id'];
            $_SESSION['user-name'] = $user['nome'];
            $_URL = 'http://localhost/blog_PHP-main/src/views/articlesView.php';
            echo json_encode($_URL);
        } else {
            echo json_encode("Erro");
        }
    } catch (Exception $e) {
        echo json_encode("Erro: " . $e->getMessage());
    } finally {
        $db->close();
    }
}
?>
