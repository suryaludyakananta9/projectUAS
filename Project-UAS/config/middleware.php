<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: " . (isset($base) ? $base : '') . "auth/login.php");
    exit();
}
?>
