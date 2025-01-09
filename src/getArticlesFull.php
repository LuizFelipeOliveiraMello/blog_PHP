<?php
header("Content-Type: application/json");
include "connectionArticles.php";

try {
    $db = new Database('mydatabase.db');
    $query = "SELECT * FROM articlee";
    $results = $db->select($query);

    $articles = array();

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $article = array(
            'html' => htmlspecialchars_decode($row['html']),
            'image' => $row['image'] // Assuming 'image' is a base64 string
        );
        $articles[] = $article;
    }

    echo json_encode($articles);
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
?>
