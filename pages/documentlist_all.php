<?php
$active_3 = 'active-page';

include 'server/db_connection.php';
include 'server/function.php';

/// ตรวจสอบว่ามีการค้นหาหรือไม่
$search_query = isset($_GET['search']) ? urldecode($_GET['search']) : '';

// ปรับการคิวรีเพื่อค้นหาจากชื่อเอกสาร
$query = "SELECT * FROM doc WHERE type = '2'";

// เพิ่มเงื่อนไขการค้นหาหากมีคำค้น
if (!empty($search_query)) {
    $query .= " AND name LIKE :search_query";
}

// เพิ่มการกรองหมวดหมู่ (ถ้ามีการเลือกหมวดหมู่)
if (isset($_GET['cat']) && $_GET['cat'] != 0) {
    $cat_filter = $_GET['cat'];
    $query .= " AND cat = :cat_filter";
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
                                        <h2>การบริหารบุคคลและการรักษาวินัย</h2>
                                    </div>
                                    <div class="page-description-actions">
                                        <a href="#" class="btn btn-primary"><i class="material-icons">source</i>ดูเอกสารทั้งหมด</a>
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
                                                    <input type="hidden" name="page" value="documentlist_cat_2"> <!-- ส่งค่า page เพื่อบอกว่าเป็นหน้าของ documentlist -->
                                                    <button type="submit" style="display: none;">Search</button> <!-- ปุ่ม submit ซ่อนเอาไว้ -->
                                                </form>

                                                <h5>ประเภทเอกสาร</h5>
                                                <ul class="list-unstyled todo-status-filter">
                                                    <li><a href="?page=documentlist_cat_2&cat=0" class="<?php echo ($cat_filter == 0) ? 'active' : ''; ?>"><i class="material-icons-outlined">format_list_bulleted</i>เอกสารทั้งหมด</a></li>
                                                    <li><a href="?page=documentlist_cat_2&cat=1" class="<?php echo ($cat_filter == 1) ? 'active' : ''; ?>"><i class="material-icons-outlined">done</i>ระเบียบ</a></li>
                                                    <li><a href="?page=documentlist_cat_2&cat=2" class="<?php echo ($cat_filter == 2) ? 'active' : ''; ?>"><i class="material-icons-outlined">pending</i>ข้อบังคับ</a></li>
                                                    <li><a href="?page=documentlist_cat_2&cat=3" class="<?php echo ($cat_filter == 3) ? 'active' : ''; ?>"><i class="material-icons-outlined">delete</i>ข้อกำหนด</a></li>
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
                                                        $id = $doc['id'];
                                                        $encoded_id = base64_encode($id);
                                                    ?>
                                                        <li class="todo-item">
                                                            <div class="todo-item-content">
                                                            <a href="index.php?page=view&id=<?php echo $encoded_id; ?>"><span class="todo-item-title"><?php echo $doc['name']; ?></span></a>
                                                                <span class="todo-item-subtitle">ประเภท : <?php echo $doc['cat']; ?> | วันที่ : <?php echo DateThai($doc['date']); ?></span>
                                                            </div>
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