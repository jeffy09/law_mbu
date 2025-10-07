<?php
session_start(); // เริ่มต้น session

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่ได้ล็อกอิน ให้ไปที่หน้า login
    $page = isset($_GET['page']) ? $_GET['page'] : 'login';
} else {
    // ถ้าล็อกอินแล้ว ให้ไปที่หน้า dashboard
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
}

switch ($page) {
    case 'home':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include 'home.php';
        break;
    case 'documentlist':
        $cat_filter = isset($_GET['cat']) ? $_GET['cat'] : 0; // กำหนดค่า cat จาก URL หรือค่าดีฟอลต์ 0
        include 'pages/documentlist_cat_1.php'; // หน้า documentlist.php
        break;
    case 'view':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include 'pages/view.php';
        break;
    case 'log_download':
        include 'pages/log_download.php';
        break;
    case 'adddocument':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include 'pages/add_doc.php';
        break;
    case 'categorydoc':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include 'pages/category_doc.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'administrator':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include 'administrator/index.php';
        break;
    case 'datatable':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        $campus = isset($_GET['campus']) ? $_GET['campus'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        include 'pages/datatable.php';
        break;
    case 'practise_datatable':
        $campus = isset($_GET['campus']) ? $_GET['campus'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        include 'pages/practise_datatable.php';
        break;
    case 'logout':
        // ลบข้อมูล session ทั้งหมดและทำลาย session
        session_unset();
        session_destroy();
        header('Location: index.php?page=login'); // กลับไปหน้า login
        exit();
        break;

    default:
        include 'login.php';
        break;
}
