<?php
include './connectionArticles.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_SESSION['user-id'])) {
        echo json_encode(['error' => 'User not logged in.']);
        exit;
    }

    if (!isset($_POST['articleId']) || !isset($_POST['html'])) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
    }

    $_ARTICLE_ID = (int)$_POST['articleId'];
    $_HTML_CONTENT = $_POST['html'];
    $_USER_ID = $_SESSION['user-id'];

    // Sanitize HTML content
    require_once 'path/to/HTMLPurifier.auto.php';
    $purifier = new HTMLPurifier();
    $_HTML_CONTENT = $purifier->purify($_HTML_CONTENT);

    try {
        $db = new Database('./mydatabase.db');

        // Check if the user is authorized to edit the article
        $queryCheck = "SELECT usuario_id FROM articles WHERE id = :id";
        $paramsCheck = array('id' => $_ARTICLE_ID);
        $resultCheck = $db->select($queryCheck, $paramsCheck);
        $rowCheck = $resultCheck->fetchArray(SQLITE3_ASSOC);

        if (!$rowCheck || $rowCheck['usuario_id'] != $_USER_ID) {
            echo json_encode(['error' => 'Unauthorized to edit this article.']);
            exit;
        }

        // Start a transaction
        $db->beginTransaction();

        // Update the article content
        $queryUpdate = "UPDATE articles SET conteudo = :conteudo WHERE id = :id";
        $paramsUpdate = array(
            'conteudo' => $_HTML_CONTENT,
            'id' => $_ARTICLE_ID
        );
        $db->query($queryUpdate, $paramsUpdate);

        // Commit the transaction
        $db->commit();

        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        if (isset($db)) {
            $db->rollback();
        }
        error_log('Error updating article: ' . $e->getMessage());
        echo json_encode(['error' => 'Error updating article: ' . $e->getMessage()]);
    } finally {
        if (isset($db)) {
            $db->close();
        }
    }
}
?>
