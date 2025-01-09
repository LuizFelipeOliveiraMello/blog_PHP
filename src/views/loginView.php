<?php
session_start();
include '../../public/login.html';

if (isset($_SESSION['user-id'])) {
    header("Location: http://localhost:80/blog_PHP-main/src/views/articlesView.php");
}
?>


<script>
    <?php require_once ("../../public/js/jquery-3.7.1.min.js") ?>
    <?php require_once ("../../public/js/login.js") ?>
</script>

<style>
    <?php require_once ("../../public/css/form.css") ?>
</style>
