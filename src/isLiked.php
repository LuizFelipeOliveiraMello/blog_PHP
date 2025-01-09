<?php
include './connectionArticles.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['articleId']) || !isset($_POST['likedBy'])) {
        echo json_encode(['error' => 'Parâmetros inválidos.']);
        exit;
    }

    $_ID_ARTICLE = (int)$_POST['articleId'];
    $_USER_ID = (int)$_POST['likedBy'];

    try {
        $db = new Database('./mydatabase.db');

        // Check if the user has already liked the article
        $query = "SELECT COUNT(*) AS count FROM likedby WHERE article_id = :article_id AND usuario_id = :usuario_id";
        $params = array(
            'article_id' => $_ID_ARTICLE,
            'usuario_id' => $_USER_ID
        );
        $results = $db->select($query, $params);
        $row = $results->fetchArray(SQLITE3_ASSOC);

        if ($row['count'] > 0) {
            echo json_encode(['message' => 'Você já curtiu']);
        } else {
            echo json_encode(['message' => 'Você ainda não curtiu']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
    } finally {
        $db->close();
    }
}
?>
