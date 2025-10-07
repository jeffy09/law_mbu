<?php
session_start();
$active_1 = 'active-page';

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}
include 'server/db_connection.php';
include 'server/function.php';


// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$query = "SELECT d.id, d.doc_id, d.name, d.cat, d.type, d.image, d.doc_status, d.doc_permission, d.event_date, d.date,
       COUNT(dl.doc_id) AS view_count
FROM doc d
JOIN document_logs dl ON d.id = dl.doc_id
GROUP BY d.id
ORDER BY view_count DESC
LIMIT 5;";
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
                                        <div class="card widget widget-stats">
                                            <div class="card-body">
                                                <div class="widget-stats-container d-flex">
                                                    <div class="widget-stats-icon widget-stats-icon-warning">
                                                        <i class="material-icons-outlined">search</i>
                                                    </div>
                                                    <form method="get" action="index.php?page=documentlist">
                                                        <input type="text" class="form-control form-control-lg" placeholder="ค้นหาเอกสาร" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                                        <input type="hidden" name="page" value="documentlist">
                                                        <button type="submit" style="display: none;">Search</button> <!-- ปุ่ม submit ซ่อนเอาไว้ -->
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="page-description-actions">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xxl-12">
                                <h5>เอกสารที่มีผู้เข้าดูมากที่สุด</h5>
                                <?php foreach ($docs as $doc):
                                    // if ($user['status'] === 'Active') {
                                    //     $STATUS = '<span class="badge bg-success">เปิดการใช้งาน</span>';
                                    // } elseif ($user['confirm_status'] == 'Suspended') {
                                    //     $STATUS = '<span class="badge bg-warning">ปิดการใช้งาน</span>';
                                    // }
                                    $value = $doc['size'];
                                    if ($value > 999 && $value <= 999999) {
                                        $size = floor($value / 1000) . ' KB';
                                    } elseif ($value > 999999) {
                                        $size = floor($value / 1000000) . ' MB';
                                    } else {
                                        $size = $value;
                                    }
                                    $id = $doc['id'];
                                    $encoded_id = base64_encode($id);
                                    $type_data = [
                                        '1' => ['การบริหารงานมหาวิทยาลัย', 'primary'],
                                        '2' => ['การบริหารบุคคลและการรักษาวินัย', 'warning'],
                                        '3' => ['สวัสดิการและสิทธิประโยชน์ของบุคลากร', 'danger'],
                                        '4' => ['การเงินและทรัพย์สิน พัสดุ', 'success'],
                                        '5' => ['การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา', 'info'],
                                        '6' => ['พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย', 'dark']
                                    ];
                                    list($type_name, $type_icon) = $type_data[$doc['type']] ?? ['', ''];
                                    $cat = $doc['cat'];
                                    $cat_map = [
                                        '1' => ['ระเบียบ'],
                                        '2' => ['ข้อบังคับ'],
                                        '3' => ['ข้อกำหนด'],
                                        '4' => ['ประกาศ'],
                                        '5' => ['พระราชบัญญัติ']
                                    ];

                                    if (isset($cat_map[$cat])) {
                                        list($cat_name) = $cat_map[$cat];
                                    }

                                    $countDocView = countDocumentsByView($connection, $id);
                                    $countDocDownload = countDocumentsByDownload($connection, $id);
                                ?>

                                    <div class="card widget widget-stats">
                                        <div class="card-body">
                                            <div class="widget-stats-container d-flex">
                                                <i class="material-icons-outlined text-<?php echo $type_icon; ?> align-middle m-r-sm">description</i>
                                                <div class="widget-stats-content flex-fill">
                                                    <a href="index.php?page=view&id=<?php echo $encoded_id; ?>" class="file-manager-recent-item-title flex-fill"><?php echo $doc['name']; ?></a>
                                                    <span class="widget-stats-info">หมวดหมู่ : <?php echo $type_name; ?> | ประเภท : <?php echo $cat_name; ?> | ดาวน์โหลด <?= $countDocDownload; ?> ครั้ง | เข้าดู <?= $countDocView; ?> ครั้ง</span>
                                                </div>
                                                <span class="p-h-sm"></span>
                                                <!-- <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="file-manager-recent-5">
                                                    <li><a class="dropdown-item" href="#">Share</a></li>
                                                    <li><a class="dropdown-item" href="#">Download</a></li>
                                                    <li><a class="dropdown-item" href="#">Move to folder</a></li>
                                                </ul> -->
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="section-description">
                            <h1>เอกสารแยกตามหมวดหมู่</h1>
                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card file-manager-group">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="material-icons text-dark">folder</i>
                                        <div class="file-manager-group-info flex-fill">
                                            <a href="index.php?page=documentlist&type=6" class="file-manager-group-title">พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย พ.ศ. ๒๕๔๐</a>
                                            <span class="file-manager-group-about"><?php echo $countDocType_6; ?> files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <script src="assets/js/pages/dashboard.js"></script>
</body>

</html>