<?php
header("Content-Type: application/json");
include "../connectionArticles.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $_name = $_POST["nome"];
    $_email = $_POST["email"];
    $_password = $_POST["senha"];

    try {
        $db = new Database('../mydatabase.db');

        // Check email uniqueness
        $queryCheckEmail = "SELECT * FROM users WHERE email = :email";
        $paramsCheckEmail = array('email' => $_email);
        $results = $db->select($queryCheckEmail, $paramsCheckEmail);

        if ($results->fetchArray(SQLITE3_ASSOC)) {
            echo json_encode("Email já está sendo usado.");
            exit;
        }

        // Validate input lengths
        if (strlen($_name) > 10) {
            echo json_encode("Nome muito grande.");
            exit;
        } elseif (strlen($_password) < 8) {
            echo json_encode("Senha muito pequena.");
            exit;
        }

        // Hash the password
        $passHash = password_hash($_password, PASSWORD_DEFAULT);

        // Insert new user
        $queryInsertUser = "INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)";
        $paramsInsertUser = array(
            'nome' => $_name,
            'email' => $_email,
            'senha' => $passHash
        );
        $db->query($queryInsertUser, $paramsInsertUser);

        echo json_encode("Usuário registrado com sucesso.");
    } catch (Exception $e) {
        echo json_encode("Erro ao registrar usuário: " . $e->getMessage());
    } finally {
        $db->close();
    }
}
?>
