<?php
include '../config.php'; // DB connection

header("Content-Type: application/json");

$sql = "SELECT surah_number, surah_name_en, surah_name_ar, ayah_count FROM surahs";
$result = $conn->query($sql);

$surahs = [];
while($row = $result->fetch_assoc()) {
    $surahs[] = $row;
}
echo json_encode($surahs, JSON_UNESCAPED_UNICODE);
