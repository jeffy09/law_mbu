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


if (!isset($_GET['id'])) {
    die("ไม่พบ ID");
}
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $doc_id = base64_decode($_GET['id']);

    // เพิ่ม Log ลงฐานข้อมูล
    $query = "INSERT INTO document_logs (user_id, doc_id) VALUES (:user_id, :doc_id)";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':doc_id', $doc_id);
    $stmt->execute();
}
// ถอดรหัส ID ที่ส่งมาใน URL
$encoded_id = $_GET['id'];
$id = base64_decode($encoded_id); // ถอดรหัส ID

// ตรวจสอบว่า ID ถูกถอดรหัสสำเร็จหรือไม่
if (!$id) {
    die("ข้อมูล ID ไม่ถูกต้อง");
}

// คิวรีข้อมูลจากฐานข้อมูลด้วย ID ที่ถอดรหัสแล้ว
$query = "SELECT * FROM doc WHERE id = :id";
$stmt = $connection->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$docs = $stmt->fetch(PDO::FETCH_ASSOC);
$type = $docs['type'];
$cat = $docs['cat'];
$type_map = [
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

$cat_map = [
    '1' => ['ระเบียบ'],
    '2' => ['ข้อบังคับ'],
    '3' => ['ข้อกำหนด']
];

if (isset($cat_map[$cat])) {
    list($cat_name) = $cat_map[$cat];
}

if (!$docs) {
    die("ไม่พบข้อมูลผู้ใช้");
}

$countDocView = countDocumentsByView($connection, $id);
$countDocDownload = countDocumentsByDownload($connection, $id);
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
                                                    <input type="hidden" name="page" value="documentlist"> <!-- ส่งค่า page เพื่อบอกว่าเป็นหน้าของ documentlist -->
                                                    <button type="submit" style="display: none;">Search</button> <!-- ปุ่ม submit ซ่อนเอาไว้ -->
                                                </form>
                                                <h5>ประเภทเอกสารในหมวดหมู่</h5>
                                                <ul class="list-unstyled todo-status-filter">
                                                    <li><a href="?page=documentlist&type=<?php echo $docs['type']; ?>&cat=0" class="<?php echo ($cat_filter == 0) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>เอกสารทั้งหมด</a></li>
                                                    <?php if ($type != '6'): ?>
                                                        <li><a href="?page=documentlist&type=<?php echo $docs['type']; ?>&cat=1" class="<?php echo ($cat_filter == 1) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ระเบียบ</a></li>
                                                        <li><a href="?page=documentlist&type=<?php echo $docs['type']; ?>&cat=2" class="<?php echo ($cat_filter == 2) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ข้อบังคับ</a></li>
                                                        <?php if (($type == '1') || ($type == '2')): ?>
                                                            <li><a href="?page=documentlist&type=<?php echo $docs['type']; ?>&cat=3" class="<?php echo ($cat_filter == 3) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ข้อกำหนด</a></li>
                                                        <?php endif; ?>
                                                        <?php if (($type == '4') || ($type == '5')): ?>
                                                            <li><a href="?page=documentlist&type=<?php echo $docs['type']; ?>&cat=4" class="<?php echo ($cat_filter == 4) ? 'active' : ''; ?>"><i class="material-icons-outlined align-middle m-r-sm">description</i>ประกาศ</a></li>
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
                                            <?php
                                            $pdfFilePath = 'pages/upload/' . $docs['image'];
                                            ?>

                                            <div class="todo-list">
                                                <li class="todo-item">
                                                    <div class="todo-item-content">
                                                        <span class="todo-item-title-2"><?php echo $docs['name']; ?></span>
                                                        <span class="todo-item-subtitle">หมวดหมู่ : <?php echo $type_name; ?> | ประเภท : <?php echo $cat_name; ?>
                                                        </span>
                                                        <div class="mailbox-open-actions">
                                                            <?php
                                                            $document_id = $id;
                                                            ?>
                                                            <a href="<?php echo $pdfFilePath; ?>"
                                                                download
                                                                class="btn btn-primary"
                                                                onclick="logDownload(<?php echo $user_id; ?>, <?php echo $document_id; ?>)">
                                                                ดาวน์โหลดไฟล์ PDF
                                                            </a>
                                                            <span class="badge badge-style-bordered rounded-pill badge-primary">ดาวน์โหลด <?= $countDocDownload;?> ครั้ง</span>
                                                            <span class="badge badge-style-bordered rounded-pill badge-success">เข้าดู <?= $countDocView;?> ครั้ง</span>
                                                        </div>
                                                    </div>
                                                </li>

                                                <div id="pdf-container"></div>


                                                <!-- <div class="pdf" id="example1"></div>
                                                <script src="server/pdf/pdfobject.js"></script>
                                                <script>
                                                    PDFObject.embed("pages/upload/<?php echo $docs['image']; ?>", "#example1");
                                                </script> -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const url = '<?php echo $pdfFilePath; ?>';

        // โหลด PDF
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            const pdfDoc = pdfDoc_;
            console.log("PDF loaded");

            // แสดงทุกหน้าใน PDF
            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                pdfDoc.getPage(pageNum).then(function(page) {
                    console.log("Page " + pageNum + " loaded");

                    // สร้าง canvas สำหรับแสดงหน้า PDF แต่ละหน้า
                    const canvas = document.createElement('canvas');
                    document.getElementById('pdf-container').appendChild(canvas); // เพิ่ม canvas ลงใน div

                    const context = canvas.getContext('2d');

                    const viewport = page.getViewport({
                        scale: 1.5 // เพิ่ม scale เพื่อให้ชัดขึ้น
                    });

                    // ใช้ devicePixelRatio เพื่อให้แสดงผลชัดขึ้นในหน้าจอความละเอียดสูง
                    const scaleFactor = window.devicePixelRatio || 1;
                    const containerWidth = document.getElementById('pdf-container').offsetWidth;
                    const scale = containerWidth / viewport.width * scaleFactor; // ปรับ scale เพื่อความชัดเจน

                    const newViewport = page.getViewport({
                        scale: scale
                    });

                    canvas.height = newViewport.height;
                    canvas.width = newViewport.width;

                    // ปรับตำแหน่งของ canvas เพื่อไม่ให้มีพื้นที่ว่างเกิน
                    canvas.style.margin = 0; // ลบ margin ถ้ามี

                    // Render page
                    const renderContext = {
                        canvasContext: context,
                        viewport: newViewport
                    };
                    page.render(renderContext);
                });
            }
        });

        // ฟังก์ชันสำหรับคัดลอกลิงก์ของหน้าปัจจุบัน
        document.getElementById('copy-link-btn').addEventListener('click', function() {
            const currentUrl = window.location.href; // ดึง URL ของหน้าปัจจุบัน
            const input = document.createElement('input');
            input.setAttribute('value', currentUrl);
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);

            // แสดง SweetAlert ที่หายไปเองหลังจาก 3 วินาที
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'คัดลอกลิงก์หน้านี้แล้ว!',
                icon: 'success',
                timer: 2000, // ปรับให้แสดง 2 วินาที
                showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            });
        });

        function logDownload(userId, documentId) {
            fetch('index.php?page=log_download', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'user_id=' + userId + '&document_id=' + documentId
                }).then(response => response.text())
                .then(data => console.log('Log saved:', data))
                .catch(error => console.error('Error:', error));
        }
    </script>

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