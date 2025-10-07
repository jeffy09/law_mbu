<?php
$db = new Database();
$connection = $db->getConnection();
/** เปลี่ยนวันที่เป็นภาษาไทย */
function DateThai($strDate){
    $strYear= date("Y",strtotime($strDate))+543;
    $strMonth= date("n",strtotime($strDate));
    $strDay= date("j",strtotime($strDate));
    $strHour= date("H",strtotime($strDate));
    $strMinute= date("i",strtotime($strDate));
    $strSeconds= date("s",strtotime($strDate));
    $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
    $strMonthThai=$strMonthCut[$strMonth];
    $strYearCut = substr($strYear,2,2);
    return "$strDay $strMonthThai $strYearCut";
}

function countDocumentsByType($conn, $type) {
    $query = "SELECT COUNT(cat) FROM doc WHERE type = :type";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}
$countDocType_1 = countDocumentsByType($connection, '1');
$countDocType_2 = countDocumentsByType($connection, '2');
$countDocType_3 = countDocumentsByType($connection, '3');
$countDocType_4 = countDocumentsByType($connection, '4');
$countDocType_5 = countDocumentsByType($connection, '5');
$countDocType_6 = countDocumentsByType($connection, '6');

function countDocumentsByView($conn, $doc_id) {
    $query = "SELECT COUNT(id) FROM document_logs WHERE doc_id = :doc_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":doc_id", $doc_id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}
function countDocumentsByDownload($conn, $document_id) {
    $query = "SELECT COUNT(id) FROM download_logs WHERE document_id = :document_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":document_id", $document_id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}
?>