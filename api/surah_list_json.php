<?php
// surah-list-json.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

// --- Helpers ---
function fail($status, $msg) {
    http_response_code($status);
    echo json_encode(["ok"=>false, "error"=>$msg], JSON_UNESCAPED_UNICODE);
    exit;
}
function ok($data) {
    echo json_encode(["ok"=>true, "surahs"=>$data], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- Fetch Surah list ---
$sql = "SELECT surah_number, surah_name_en, surah_name_ar, ayah_count
        FROM surahs ORDER BY surah_number ASC";
$res = $conn->query($sql);

if (!$res) {
    fail(500, "DB query failed");
}

$surahs = [];
while ($row = $res->fetch_assoc()) {
    $surahs[] = [
        "surah_number" => intval($row['surah_number']),
        "surah_name_en" => $row['surah_name_en'],
        "surah_name_ar" => $row['surah_name_ar'],
        "ayah_count" => intval($row['ayah_count'])
    ];
}

// --- Response ---
ok($surahs);
