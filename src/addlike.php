<?php
include './connectionArticles.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['articleId']) || !isset($_POST['likedBy'])) {
        echo json_encode(array('error' => 'Parâmetros inválidos.'));
        exit;
    }

    $_ID_ARTICLE = (int)$_POST['articleId'];
    $_USER_ID = (int)$_POST['likedBy'];

    try {
        $db = new Database('./mydatabase.db');

        // Check if the user has already liked the article
        $queryCheckLike = "SELECT * FROM likedby WHERE article_id = :article_id AND usuario_id = :usuario_id";
        $paramsCheckLike = array(
            'article_id' => $_ID_ARTICLE,
            'usuario_id' => $_USER_ID
        );
        $resultsCheckLike = $db->select($queryCheckLike, $paramsCheckLike);

        if ($resultsCheckLike->fetchArray(SQLITE3_ASSOC)) {
            echo json_encode(array('error' => 'Você já curtiu.'));
            exit;
        }

        // Insert the user into likedby table
        $queryInsertLiked = "INSERT INTO likedby (article_id, usuario_id) VALUES (:article_id, :usuario_id)";
        $paramsInsertLiked = array(
            'article_id' => $_ID_ARTICLE,
            'usuario_id' => $_USER_ID
        );
        $db->query($queryInsertLiked, $paramsInsertLiked);

        // Get the updated likes count
        $queryCountLikes = "SELECT COUNT(*) AS likes FROM likedby WHERE article_id = :article_id";
        $paramsCountLikes = array('article_id' => $_ID_ARTICLE);
        $resultCountLikes = $db->select($queryCountLikes, $paramsCountLikes);
        $row = $resultCountLikes->fetchArray(SQLITE3_ASSOC);

        echo json_encode(array('status' => 'success', 'likes' => $row['likes']));

    } catch (Exception $e) {
        echo json_encode(array('error' => 'Erro: ' . $e->getMessage()));
    } finally {
        $db->close();
        exit;
    }
}
?>
