<?php
date_default_timezone_set('asia/bangkok');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: http://law.mbu.ac.th/index.php?page=login');
    exit();
} else {
    header('Location: index.php?page=home');
    exit();
}

?>
