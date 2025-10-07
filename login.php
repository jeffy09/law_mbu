<?php
define('SECURE_ACCESS', true);
session_start();
require_once 'server/encryption.php'; // ฟังก์ชันเข้ารหัส
require_once 'server/db_portal.php'; // การเชื่อมต่อฐานข้อมูล

$db = new Database();
$connection = $db->getConnection();
$error = "";

// กรณีที่ส่งข้อมูลผ่าน Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);

    if (!empty($username)) {
        $encryptedUsername = encryptData($username, $secret_key, $cipher); // เข้ารหัส
        header("Location: login.php?data=" . urlencode($encryptedUsername)); // ส่งค่าเข้ารหัสไป
        exit();
    } else {
        $error = "กรุณากรอกชื่อผู้ใช้งาน";
    }
}

// กรณีที่รับค่าจาก URL (ตรวจสอบการเข้าสู่ระบบอัตโนมัติ)
if (isset($_GET['data'])) {
    $decryptedUsername = decryptData($_GET['data'], $secret_key, $cipher);

    if (!empty($decryptedUsername)) {
        $stmt = $connection->prepare("SELECT * FROM user WHERE email = :username AND user_level = '1'");
        $stmt->bindParam(':username', $decryptedUsername);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['username'];
            $_SESSION['username'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect ไปยังหน้า dashboard
            header('Location: index.php?page=home');
            exit();
        } else {
            $error = "ไม่พบชื่อผู้ใช้ในระบบ";
        }
    } else {
        $error = "เกิดข้อผิดพลาดในการถอดรหัส";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Admin Dashboard Template">
    <meta name="keywords" content="admin,dashboard">
    <meta name="author" content="stacks">
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title>กฎหมายลำดับรอง มหาวิทยาลัยมหามกุฏราชวิทยาลัย</title>

    <!-- Styles -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/plugins/perfectscroll/perfect-scrollbar.css" rel="stylesheet">
    <link href="assets/plugins/pace/pace.css" rel="stylesheet">


    <!-- Theme Styles -->
    <link href="assets/css/main.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/neptune.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/neptune.png" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Toastr.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="app app-auth-sign-in align-content-stretch d-flex flex-wrap justify-content-end">
        <div class="app-auth-background">

        </div>
        <div class="app-auth-container">
            <div class="logo">
                <a href="index.html"></a>
            </div>
            <h5>กฎหมายลำดับรอง มหาวิทยาลัยมหามกุฏราชวิทยาลัย</h5>
            <p class="auth-description">กรุณาลงชื่อเพื่อเข้าสู่ระบบ</p>
            <script>
                <?php if (!empty($error)) : ?>
                    toastr.error('<?php echo $error; ?>', 'ข้อผิดพลาด', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-top-right",
                        timeOut: 5000
                    });
                <?php endif; ?>
            </script>
            <form method="POST">
                <div class="auth-credentials m-b-xxl">
                    <label for="signInEmail" class="form-label">กรอกอีเมลบุคลากรมหาวิทยาลัย (@mbu.ac.th)</label>
                    <input type="text" class="form-control m-b-md" id="signInEmail" name="username" aria-describedby="signInEmail" placeholder="">

                    <!-- <label for="signInPassword" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" id="signInPassword" name="password" aria-describedby="signInPassword" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"> -->
                </div>

                <div class="auth-submit">
                    <button type="submit" class="btn btn-primary">ยืนยัน</button>
                    <!-- <a href="#" class="auth-forgot-password float-end">Forgot password?</a> -->
                </div>
            </form>
            <div class="divider"></div>
            <!-- <div class="auth-alts">
                <a href="#" class="auth-alts-google"></a>
                <a href="#" class="auth-alts-facebook"></a>
                <a href="#" class="auth-alts-twitter"></a>
            </div> -->
        </div>
    </div>

    <!-- Javascripts -->
    <script src="assets/plugins/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/plugins/perfectscroll/perfect-scrollbar.min.js"></script>
    <script src="assets/plugins/pace/pace.min.js"></script>
    <script src="assets/js/main.min.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>