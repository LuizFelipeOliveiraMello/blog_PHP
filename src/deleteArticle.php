<?php
include './connectionArticles.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['articleId']) && is_numeric($_POST['articleId'])) {
        $_ARTICLE_TO_DELETE = (int)$_POST['articleId'];

        try {
            $db = new Database('./mydatabase.db');

            // Check if the article exists
            $queryCheck = "SELECT COUNT(*) FROM articles WHERE id = :id";
            $paramsCheck = array('id' => $_ARTICLE_TO_DELETE);
            $resultCheck = $db->select($queryCheck, $paramsCheck);
            $rowCheck = $resultCheck->fetchArray(SQLITE3_NUM);
            if ($rowCheck[0] == 0) {
                echo json_encode(array('error' => 'Artigo não encontrado.'));
                exit;
            }

            $db->beginTransaction();

            $db->query('DELETE FROM articles WHERE id = :id', array('id' => $_ARTICLE_TO_DELETE));
            $db->query('DELETE FROM likedby WHERE article_id = :id', array('id' => $_ARTICLE_TO_DELETE));
            $db->query('DELETE FROM comments WHERE article_id = :id', array('id' => $_ARTICLE_TO_DELETE));

            $db->commit();

            echo json_encode(array('status' => 'success'));

        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(array('error' => 'Erro ao deletar: ' . $e->getMessage()));
        } finally {
            $db->close();
            exit;
        }
    } else {
        echo json_encode(array('error' => 'ID do artigo inválido.'));
        exit;
    }
}
?>
