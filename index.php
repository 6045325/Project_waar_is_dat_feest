<?php
session_start();
if (!empty($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login/login.php');
}
exit;
?>
