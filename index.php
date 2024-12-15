<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: game.php"); 
    exit;
} else {
    header("Location: login.php"); 
    exit;
}
?>
