<?php
header("Content-Type: application/json");
include "./connectionArticles.php";

try {
    // Ensure the path to the database is correct
    $db = new Database('./mydatabase.db');

    // Adjusted SQL query with joins and grouping
    $query = "
        SELECT 
            articles.id, 
            articles.conteudo AS html, 
            COUNT(likedby.id) AS likes, 
            users.nome AS por
        FROM 
            articles
        LEFT JOIN 
            users ON articles.usuario_id = users.id
        LEFT JOIN 
            likedby ON articles.id = likedby.article_id
        GROUP BY 
            articles.id, articles.conteudo, users.nome
    ";

    $results = $db->select($query);

    // Collect data for each article in a single array
    $_ARTICLES = array();

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $_ARTICLES[] = array(
            'id' => $row['id'],
            'html' => $row['html'],
            'likes' => $row['likes'],
            'por' => $row['por']
        );
    }

    // Encode the array of articles into JSON
    echo json_encode($_ARTICLES);

} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
} finally {
    $db->close();
}
?>
