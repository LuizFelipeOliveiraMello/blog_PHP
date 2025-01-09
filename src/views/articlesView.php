<?php
session_start();
if (!isset($_SESSION["user-id"])) {
    header('Location: http://localhost:80/blog_PHP-main/src/views/loginView.php');
    exit;
}

include '../../public/index.html';
?>

<script>
    <?php require_once ("../../public/js/jquery-3.7.1.min.js") ?>
    <?php require_once ("../../public/js/index.js") ?>
</script>

<style>
    <?php require_once ("../../public/css/main.css") ?>
</style>
