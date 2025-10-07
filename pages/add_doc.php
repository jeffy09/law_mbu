<?php
session_start();
$active_8 = 'active-page';
// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เชื่อมต่อกับฐานข้อมูล
    include_once('server/db_connection.php');
    $db = new Database();
    $conn = $db->getConnection();

    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!$conn) {
        die("การเชื่อมต่อฐานข้อมูลล้มเหลว");
    }

    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $cat = $_POST['cat'];
    $type = $_POST['type'];

    // สร้าง doc_id อัตโนมัติ
    $doc_id = "MBU_" . $cat . "_" . $type . "_" . date("YmdHis");

    // ตรวจสอบไฟล์ PDF และขนาดไฟล์
    if ($_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = $file['type'];

        // ตรวจสอบว่าเป็นไฟล์ PDF หรือไม่
        if ($file_type != 'application/pdf') {
            $error_message = "กรุณาอัพโหลดไฟล์ PDF เท่านั้น";
        }
        // ตรวจสอบขนาดไฟล์ไม่เกิน 15MB
        elseif ($file_size > 15 * 1024 * 1024) {
            $error_message = "ขนาดไฟล์ต้องไม่เกิน 15MB";
        } else {
            // ใช้ doc_id เป็นชื่อไฟล์และตั้งนามสกุลเป็น .pdf
            $file_name = $doc_id . ".pdf";

            // กำหนดเส้นทางที่จะบันทึกไฟล์
            $upload_dir = 'pages/upload/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $upload_dir . $file_name;

            // อัพโหลดไฟล์ไปยัง directory ที่กำหนด
            if (move_uploaded_file($file_tmp_name, $file_path)) {
                // สร้างคำสั่ง SQL สำหรับการเพิ่มข้อมูลลงในฐานข้อมูล
                try {
                    $sql = "INSERT INTO doc (doc_id, name, cat, type, image, doc_status, doc_permission, event_date)
                            VALUES (:doc_id, :name, :cat, :type, :image, DEFAULT, DEFAULT, CURRENT_TIMESTAMP)";
                    $stmt = $conn->prepare($sql);

                    // Bind parameters to the query
                    $stmt->bindParam(':doc_id', $doc_id);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':cat', $cat);
                    $stmt->bindParam(':type', $type);
                    $stmt->bindParam(':image', $file_name);

                    // Execute the statement
                    if ($stmt->execute()) {
                        $success_message = "ข้อมูลถูกเพิ่มสำเร็จ";
                    } else {
                        $error_message = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล";
                    }
                } catch (Exception $e) {
                    $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
                }
            } else {
                $error_message = "ไม่สามารถอัพโหลดไฟล์ได้";
            }
        }
    } else {
        $error_message = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn = null;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('partials/head.php'); ?>
</head>

<body>
    <div class="app align-content-stretch d-flex flex-wrap">
        <div class="app-sidebar">
            <?php include('partials/sidebar.php'); ?>
        </div>
        <div class="app-container">
            <div class="search">
                <form>
                    <input class="form-control" type="text" placeholder="Type here..." aria-label="Search">
                </form>
                <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
            </div>
            <div class="app-header">
                <?php include('partials/header.php'); ?>
            </div>
            <div class="app-content">
                <div class="content-wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <div class="page-description d-flex align-items-center">
                                    <div class="page-description-content flex-grow-1">
                                        <h2>เพิ่มเอกสาร</h2>
                                    </div>
                                    <!-- <div class="page-description-actions">
                                        <a href="index.php?page=documentlist" class="btn btn-primary"><i class="material-icons">source</i>ดูเอกสารทั้งหมด</a>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                    </div>
                                    <div class="card-body">

                                        <div class="example-container">
                                            <form action="index.php?page=adddocument" method="POST" enctype="multipart/form-data">
                                                <div class="example-content">
                                                    <label for="exampleInputname" class="form-label">ชื่อเอกสาร</label>
                                                    <textarea class="form-control" id="name" name="name" required></textarea>
                                                </div>
                                                <div class="example-content">
                                                    <label for="exampleInputname" class="form-label">หมวดหมู่เอกสาร</label>
                                                    <select class="form-select" id="type" name="type" aria-label="Default select example">
                                                        <option selected>กรุณาเลือก</option>
                                                        <option value="1">การบริหารงานมหาวิทยาลัย</option>
                                                        <option value="2">การบริหารบุคคลและการรักษาวินัย</option>
                                                        <option value="3">สวัสดิการและสิทธิประโยชน์ของบุคลากร</option>
                                                        <option value="4">การเงินและทรัพย์สิน พัสดุ</option>
                                                        <option value="5">การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา</option>
                                                    </select>
                                                </div>
                                                <div class="example-content">
                                                    <label for="exampleInputname" class="form-label">ประเภทเอกสาร</label>
                                                    <select class="form-select" id="cat" name="cat" aria-label="Default select example">
                                                        <option selected>กรุณาเลือก</option>
                                                        <option value="1">ระเบียบ</option>
                                                        <option value="2">ข้อบังคับ</option>
                                                        <option value="3">ข้อกำหนด</option>
                                                        <option value="4">ประกาศ</option>
                                                    </select>
                                                </div>
                                                <div class="example-content">
                                                    <label for="exampleInputname" class="form-label">อัพโหลดไฟล์ (PDF):</label>
                                                    <input class="form-control" type="file" id="image" name="image" accept=".pdf" required>
                                                </div>
                                                <div class="example-content">
                                                    <input class="btn btn-primary" type="submit" value="เพิ่มเอกสาร">
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascripts -->
    <script src="assets/plugins/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/plugins/perfectscroll/perfect-scrollbar.min.js"></script>
    <script src="assets/plugins/pace/pace.min.js"></script>
    <script src="assets/plugins/highlight/highlight.pack.js"></script>
    <script src="assets/js/main.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
    <?php
    // แสดง SweetAlert ถ้ามีข้อความสำเร็จหรือข้อผิดพลาด
    if (isset($success_message)) {
        echo "<script>Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: '$success_message'
    }).then(function() {
        window.location.href = 'index.php?page=adddocument'; // รีเฟรชหน้าหลังจากแสดงผลสำเร็จ
    });</script>";
    } elseif (isset($error_message)) {
        echo "<script>Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: '$error_message'
    });</script>";
    }
    ?>

</body>

</html>