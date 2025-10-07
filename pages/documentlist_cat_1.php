<?php
session_start();
include 'server/db_connection.php';
include 'server/function.php';

/// ตรวจสอบว่ามีการค้นหาหรือไม่
$search_query = isset($_GET['search']) ? urldecode($_GET['search']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';  // ป้องกันการเข้าไม่มีกำหนดค่า 'type'
$type_map = [
    '' => ['เอกสารทั้งหมด'],
    '1' => ['การบริหารงานมหาวิทยาลัย', 'active-page', 'active_2'],
    '2' => ['การบริหารบุคคลและการรักษาวินัย', 'active-page', 'active_3'],
    '3' => ['สวัสดิการและสิทธิประโยชน์ของบุคลากร', 'active-page', 'active_4'],
    '4' => ['การเงินและทรัพย์สิน พัสดุ', 'active-page', 'active_5'],
    '5' => ['การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา', 'active-page', 'active_6'],
    '6' => ['พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย', 'active-page', 'active_9']
];

if (isset($type_map[$type])) {
    list($type_name, $active_class, $active_variable) = $type_map[$type];
    $$active_variable = $active_class;
}

// ตรวจสอบว่าเป็นประเภทไหน หรือไม่มีประเภท (ทั้งหมด)
$query = "SELECT * FROM doc";

if ($type != '') {
    $query .= " WHERE type = :type"; // ถ้ามีการเลือกประเภท
}

// เพิ่มเงื่อนไขการค้นหาหากมีคำค้น
if (!empty($search_query)) {
    $query .= isset($type) && $type != '' ? " AND name LIKE :search_query" : " WHERE name LIKE :search_query";
}

// เพิ่มการกรองหมวดหมู่ (ถ้ามีการเลือกหมวดหมู่)
if (isset($_GET['cat']) && $_GET['cat'] != 0) {
    $cat_filter = $_GET['cat'];
    $query .= isset($type) && $type != '' ? " AND cat = :cat_filter" : " WHERE cat = :cat_filter";
} else {
    $cat_filter = 0; // ถ้าไม่ได้เลือกหมวดหมู่ ก็ให้เป็น 0
}

$stmt = $connection->prepare($query);

// Bind parameter ถ้ามีคำค้นหา
if (!empty($search_query)) {
    $stmt->bindValue(':search_query', '%' . $search_query . '%');
}

// Bind parameter ถ้ามีการกรองหมวดหมู่
if ($cat_filter != 0) {
    $stmt->bindValue(':cat_filter', $cat_filter);
}

// Bind parameter ถ้ามีการเลือกประเภท
if ($type != '') {
    $stmt->bindValue(':type', $type);
}

$stmt->execute();
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณจำนวนเอกสารที่ค้นพบ
$total_docs = count($docs);


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
                                        <h2><?php echo $type_name; ?></h2>
                                    </div>
                                    <div class="page-description-actions">
                                        <a href="index.php?page=documentlist" class="btn btn-primary"><i class="material-icons">source</i>ดูเอกสารทั้งหมด</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="card todo-container">
                                    <div class="row">
                                        <div class="col-xl-4 col-xxl-3">
                                            <div class="todo-menu">
                                                <h5 class="todo-menu-title">ค้นหา</h5>
                                                <form method="get" action="index.php">
                                                    <input type="text" class="form-control form-control-solid m-b-lg" placeholder="Type here..." name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                                    <input type="hidden" name="page" value="documentlist">
                                                    <button type="submit" style="display: none;">Search</button> <!-- ปุ่ม submit ซ่อนเอาไว้ -->
                                                </form>

                                                <h5>ประเภทเอกสารในหมวดหมู่</h5>
                                                <ul class="list-unstyled todo-status-filter">
                                                    <li><a href="?page=documentlist&type=<?php echo $type; ?>&cat=0" class="<?php echo ($cat_filter == 0) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>เอกสารทั้งหมด</a></li>

                                                    <?php if ($type != '6'): ?>
                                                        <li><a href="?page=documentlist&type=<?php echo $type; ?>&cat=1" class="<?php echo ($cat_filter == 1) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ระเบียบ</a></li>
                                                        <li><a href="?page=documentlist&type=<?php echo $type; ?>&cat=2" class="<?php echo ($cat_filter == 2) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ข้อบังคับ</a></li>
                                                        <?php if (($type == '1') || ($type == '2') || ($type == '')): ?>
                                                            <li><a href="?page=documentlist&type=<?php echo $type; ?>&cat=3" class="<?php echo ($cat_filter == 3) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ข้อกำหนด</a></li>
                                                        <?php endif; ?>
                                                        <?php if (($type == '4') || ($type == '5') || ($type == '')): ?>
                                                            <li><a href="?page=documentlist&type=<?php echo $type; ?>&cat=4" class="<?php echo ($cat_filter == 4) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ประกาศ</a></li>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </ul>
                                                <hr>
                                                <h5>หมวดหมู่เอกสารอื่น ๆ</h5>
                                                <ul class="list-unstyled todo-status-filter">
                                                    <?php foreach ([6 => 'พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย', 1 => 'การบริหารงานมหาวิทยาลัย', 2 => 'การบริหารบุคคลและการรักษาวินัย', 3 => 'สวัสดิการและสิทธิประโยชน์ของบุคลากร', 4 => 'การเงินและทรัพย์สิน พัสดุ', 5 => 'การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา'] as $key => $label): ?>
                                                        <?php if ($type != $key): ?>
                                                            <li><a href="index.php?page=documentlist&type=<?= $key ?>"><i class="material-icons-outlined text-<?= ['1' => 'primary', '2' => 'warning', '3' => 'danger', '4' => 'success', '5' => 'info', '6' => 'dark'][$key] ?>">description</i><?= $label ?></a></li>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-xl-8 col-xxl-9">
                                            <div class="todo-list">
                                                <?php
                                                // แสดงผลเฉพาะเมื่อมีการค้นหา
                                                if (!empty($search_query)) {
                                                    echo "<h5>ค้นพบทั้งหมด $total_docs เอกสาร | คำที่ค้นหา: \"" . htmlspecialchars($search_query) . "\"</h5>";
                                                }
                                                ?>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($docs as $doc):
                                                        $value = $doc['size'];
                                                        if ($value > 999 && $value <= 999999) {
                                                            $size = floor($value / 1000) . ' KB';
                                                        } elseif ($value > 999999) {
                                                            $size = floor($value / 1000000) . ' MB';
                                                        } else {
                                                            $size = $value;
                                                        }
                                                        $type = $doc['type'];
                                                        $type_map = [
                                                            '1' => ['การบริหารงานมหาวิทยาลัย'],
                                                            '2' => ['การบริหารบุคคลและการรักษาวินัย'],
                                                            '3' => ['สวัสดิการและสิทธิประโยชน์ของบุคลากร'],
                                                            '4' => ['การเงินและทรัพย์สิน พัสดุ'],
                                                            '5' => ['การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา'],
                                                            '6' => ['พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย']
                                                        ];
                                                        if (isset($type_map[$type])) {
                                                            list($type_name) = $type_map[$type];
                                                        }
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

                                                        if ($doc['doc_status'] == '1') {
                                                            $doc_status = '| สถานะ : <span class="badge badge-success badge-style-light">เผยแพร่</span>';
                                                        }
                                                        if ($doc['doc_status'] == '0') {
                                                            $doc_status = '| สถานะ : <span class="badge badge-danger badge-style-light">ระงับการเผยแพร่</span>';
                                                        }


                                                        $id = $doc['id'];
                                                        $encoded_id = base64_encode($id);
                                                        $countDocView = countDocumentsByView($connection, $id);
                                                        $countDocDownload = countDocumentsByDownload($connection, $id);
                                                    ?>
                                                        <li class="todo-item">
                                                            <div class="todo-item-content">
                                                                <a href="index.php?page=view&id=<?php echo $encoded_id; ?>"><span class="todo-item-title"><?php echo $doc['name']; ?></span></a>
                                                                <span class="todo-item-subtitle">หมวดหมู่ : <?php echo $type_name; ?> | ประเภท : <?php echo $cat_name; ?> | ดาวน์โหลด <?= $countDocDownload; ?> ครั้ง | เข้าดู <?= $countDocView; ?> ครั้ง
                                                                </span>
                                                            </div>
                                                            <?php if ($_SESSION['role'] == 'Admin'): ?>
                                                                <a href="#" class="widget-files-list-item-download-btn">
                                                                    <i class="material-icons-outlined text-info">
                                                                        edit_note
                                                                    </i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
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