<div class="logo">
    <div id="mobileshow"><a href="home" class="logo-icon"></a></div>
    <center><img src="assets/images/neptune.png" height="100px" class="nav-item hidden-on-mobile"></center>
    <!-- <div class="sidebar-user-switcher user-activity-online">
        <a href="#">
            <img src="assets/images/avatars/avatar.png">
            <span class="activity-indicator"></span>
            <span class="user-info-text">MBU<br><span class="user-state-info">Documents</span></span>
        </a>
    </div> -->
</div>
<div class="app-menu">
    <ul class="accordion-menu">
        <li class="sidebar-title">
            Home
        </li>
        <li class="<?php echo $active_1; ?>">
            <a href="home"><i class="material-icons-two-tone">home</i>หน้าแรก</a>
        </li>
        <li class="sidebar-title">
            หมวดหมู่
        </li>
        <li class="<?php echo $active_9; ?>">
            <a href="index.php?page=documentlist&type=6" data-bs-toggle="tooltip" data-bs-placement="top" title="พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย พ.ศ. ๒๕๔๐"><i class="material-icons-two-tone">text_snippet</i>พระราชบัญญัติมหาวิทยาลัยมหามกุฏราชวิทยาลัย พ.ศ. ๒๕๔๐</a>
        </li>
        <li class="<?php echo $active_2; ?>">
            <a href="index.php?page=documentlist&type=1" data-bs-toggle="tooltip" data-bs-placement="top" title="การบริหารงานมหาวิทยาลัย"><i class="material-icons-two-tone">text_snippet</i>การบริหารงานมหาวิทยาลัย</a>
            <!-- <ul class="sub-menu">
                <li>
                    <a href="styles-typography.html">ระเบียบ</a>
                </li>
                <li>
                    <a href="styles-code.html">ข้อบังคับ</a>
                </li>
                <li>
                    <a href="styles-icons.html">ข้อกำหนด</a>
                </li>
            </ul> -->
        </li>
        <li class="<?php echo $active_3; ?>">
            <a href="index.php?page=documentlist&type=2" data-bs-toggle="tooltip" data-bs-placement="top" title="การบริหารบุคคลและการรักษาวินัย"><i class="material-icons-two-tone">text_snippet</i>การบริหารบุคคลและการรักษาวินัย</a>
            <!-- <ul class="sub-menu">
                <li>
                    <a href="styles-typography.html">ระเบียบ</a>
                </li>
                <li>
                    <a href="styles-code.html">ข้อบังคับ</a>
                </li>
                <li>
                    <a href="styles-icons.html">ข้อกำหนด</a>
                </li>
            </ul> -->
        </li>
        <li class="<?php echo $active_4; ?>">
            <a href="index.php?page=documentlist&type=3" data-bs-toggle="tooltip" data-bs-placement="top" title="สวัสดิการและสิทธิประโยชน์ของบุคลากร"><i class="material-icons-two-tone">text_snippet</i>สวัสดิการและสิทธิประโยชน์ของบุคลากร</a>
        </li>
        <li class="<?php echo $active_5; ?>">
            <a href="index.php?page=documentlist&type=4" data-bs-toggle="tooltip" data-bs-placement="top" title="การเงินและทรัพย์สิน พัสดุ"><i class="material-icons-two-tone">text_snippet</i>การเงินและทรัพย์สิน พัสดุ</a>
        </li>
        <li class="<?php echo $active_6; ?>">
            <a href="index.php?page=documentlist&type=5" data-bs-toggle="tooltip" data-bs-placement="top" title="การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา"><i class="material-icons-two-tone">text_snippet</i>การศึกษา การวิจัย วิชาการ ตำแหน่งทางวิชาการ และกิจการนักศึกษา</a>
        </li>
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <li class="sidebar-title">
                Manage Docs
            </li>
            <li class="<?php echo $active_7; ?>">
                <a href="index.php?page=categorydoc"><i class="material-icons-two-tone">file_open</i>จัดการเอกสาร</a>
            </li>

            <li class="<?php echo $active_8; ?>">
                <a href="index.php?page=adddocument"><i class="material-icons-two-tone">difference</i>เพิ่มเอกสาร</a>
            </li>
        <?php endif; ?>
        <!-- <li class="sidebar-title">
            Other
        </li>
        <li>
            <a href="#"><i class="material-icons-two-tone">bookmark</i>Documentation</a>
        </li>
        <li>
            <a href="#"><i class="material-icons-two-tone">access_time</i>Change Log</a>
        </li> -->
    </ul>
</div>