<?php
include './connectionArticles.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_SESSION['user-id']) || !isset($_POST['articleId']) || !isset($_POST['content'])) {
        echo json_encode(array('error' => 'Parâmetros inválidos.'));
        exit;
    }

    $_USER_ID = $_SESSION['user-id'];
    $_ARTICLEID = (int)$_POST['articleId'];
    $_CONTENT = $_POST['content'];

    try {
        $db = new Database('./mydatabase.db');

        // Check if the article exists
        $checkArticleQuery = "SELECT id FROM articles WHERE id = :id";
        $checkArticleParams = array('id' => $_ARTICLEID);
        $articleResult = $db->select($checkArticleQuery, $checkArticleParams);
        if (!$articleResult->fetchArray(SQLITE3_ASSOC)) {
            echo json_encode(array('error' => 'Artigo não encontrado.'));
            exit;
        }

        // Insert the comment
        $query = "INSERT INTO comments (article_id, comentario, usuario_id, data_comentario) 
                  VALUES (:article_id, :comentario, :usuario_id, DATETIME('now'))";
        $params = array(
            'article_id' => $_ARTICLEID,
            'comentario' => $_CONTENT,
            'usuario_id' => $_USER_ID
        );
        $db->query($query, $params);

        // Optionally, return the comment ID
        $_COMMENT_ID = $db->lastInsertRowID();
        echo json_encode(array('status' => 'success', 'content' => $_CONTENT, 'comment_id' => $_COMMENT_ID));

    } catch (Exception $e) {
        echo json_encode(array('error' => 'Erro: ' . $e->getMessage()));
    } finally {
        $db->close();
        exit;
    }
}
?>
