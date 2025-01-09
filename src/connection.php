<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'usersblog';

define('DB_HOST', 'db'); // Nome do serviço no docker-compose
define('DB_USER', 'blog_user'); // Nome do usuário definido no docker-compose
define('DB_PASS', 'blog_password'); // Senha definida no docker-compose
define('DB_NAME', 'blog');

// Conecta ao servidor MySQL
$conexao = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica se a conexão foi bem-sucedida
if (!$conexao) {
    die('Erro na conexão: ' . mysqli_connect_error());
}
?>
