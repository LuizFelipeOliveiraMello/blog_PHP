<?php
include "./connectionArticles.php";
session_start();

if (isset($_SESSION['user-id'])) {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        // Assuming 'titulo' and 'html' are sent in POST data
        $titulo = isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : 'Untitled Article';
        $conteudo = htmlspecialchars($_POST['html']);
        $usuario_id = $_SESSION['user-id'];

        try {
            $db = new Database('./mydatabase.db');

            // Enable foreign key suppo

            // Prepare the INSERT query with correct table and column names
            $query = "INSERT INTO articles(titulo, conteudo, data_de_publicacao, usuario_id) 
                      VALUES (:titulo, :conteudo, DATETIME('now'), :usuario_id)";
            $params = array(
                'titulo' => $titulo,
                'conteudo' => $conteudo,
                'usuario_id' => $usuario_id
            );

            $db->query($query, $params);

            // Get the last insert ID
            $_nextId = $db->lastInsertRowID();

            // Optionally handle images if an 'images' table is added to the schema
            // foreach ($_POST['image'] as $key => $value) {
            //     $queryImages = "INSERT INTO images(article_id, src) VALUES (:article_id, :src)";
            //     $paramsImages = array(
            //         'article_id' => $_nextId,
            //         'src' => $value
            //     );
            //     $db->query($queryImages, $paramsImages);
            // }

            // Send JSON response with article content and ID
            echo json_encode(array('id' => $_nextId, 'titulo' => $titulo, 'conteudo' => $conteudo));

            $db->close();

            exit;
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }
} else {
    echo json_encode(array('error' => 'User not logged in.'));
    exit;
}
?>
