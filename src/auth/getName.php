<?php 
    session_start();
    if($_SERVER["REQUEST_METHOD"] == 'GET')
    {
        $_NAME = $_SESSION['user-name'];
        echo json_encode($_NAME);
    }

?>