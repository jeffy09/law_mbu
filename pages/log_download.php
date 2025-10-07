<?php
session_start();
// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}
include 'server/db_connection.php';
include 'server/function.php';
$db = new Database();
$connection = $db->getConnection();

if (isset($_SESSION['user_id'])) {
    $user_id = $_POST['user_id'];
    $document_id = $_POST['document_id'];

    $query = "INSERT INTO download_logs (user_id, document_id) VALUES (:user_id, :document_id)";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':document_id', $document_id);
    $stmt->execute();


}

