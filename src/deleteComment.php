<?php
include './connectionArticles.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_SESSION['user-id'])) {
        echo json_encode(array('error' => 'User not logged in.'));
        exit;
    }

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(array('error' => 'Invalid comment ID.'));
        exit;
    }

    $_COMMENT_TO_DELETE = (int)$_POST['id'];
    $_USER_ID = $_SESSION['user-id'];

    try {
        $db = new Database('./mydatabase.db');

        // Check if the comment exists and belongs to the user
        $queryCheck = "SELECT COUNT(*) FROM comments WHERE id = :id AND usuario_id = :user_id";
        $paramsCheck = array('id' => $_COMMENT_TO_DELETE, 'user_id' => $_USER_ID);
        $resultCheck = $db->select($queryCheck, $paramsCheck);
        $rowCheck = $resultCheck->fetchArray(SQLITE3_NUM);
        if ($rowCheck[0] == 0) {
            echo json_encode(array('error' => 'Comment not found or you do not have permission to delete it.'));
            exit;
        }

        // Delete the comment
        $queryDelete = "DELETE FROM comments WHERE id = :id";
        $paramsDelete = array('id' => $_COMMENT_TO_DELETE);
        $db->query($queryDelete, $paramsDelete);

        echo json_encode(array('status' => 'success'));

    } catch (Exception $e) {
        echo json_encode(array('error' => 'Error deleting comment: ' . $e->getMessage()));
    } finally {
        $db->close();
        exit;
    }
}
?>
