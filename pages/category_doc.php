<?php
session_start();
// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}
$active_7 = 'active-page';

include 'server/db_connection.php';
include 'server/function.php';


// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$query = "SELECT * FROM doc ORDER BY id DESC LIMIT 5";
$stmt = $connection->prepare($query);
$stmt->execute();
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
                                        <h2>หมวดหมู่เอกสาร</h2>
                                    </div>
                                    <div class="page-description-actions">
                                        <form method="get" action="index.php?page=documentlist">
                                            <input type="text" class="form-control form-control-solid m-b-lg" placeholder="ค้นหาเอกสาร" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                            <input type="hidden" name="page" value="documentlist">
                                            <button type="submit" style="display: none;">Search</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-primary">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=1" class="file-manager-group-title">การบริหารงานมหาวิทยาลัย</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_1; ?> files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-warning">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=2" class="file-manager-group-title">การบริหารบุคคลและการรักษาวินัย</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_2; ?> files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-danger">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=3" class="file-manager-group-title">สวัสดิการและสิทธิประโยชน์ของบุคลากร</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_3; ?> files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-success">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=4" class="file-manager-group-title">การเงินและทรัพย์สิน พัสดุ</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_4; ?> files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-info">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=5" class="file-manager-group-title">การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_5; ?> files</span>
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
</body>

</html>