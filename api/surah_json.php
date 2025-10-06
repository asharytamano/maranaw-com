<?php
// surah-json.php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

// --- Helpers ---
function fail($status, $msg) {
    http_response_code($status);
    echo json_encode(["ok"=>false, "error"=>$msg], JSON_UNESCAPED_UNICODE);
    exit;
}
function ok($data) {
    echo json_encode(["ok"=>true, "data"=>$data], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- Validate input ---
$surah = isset($_GET['surah']) ? intval($_GET['surah']) : 0;
if ($surah < 1 || $surah > 114) fail(400, "Invalid surah number");

// --- Fetch Surah meta ---
$stmt = $conn->prepare("SELECT surah_name_en, surah_name_ar, ayah_count FROM surahs WHERE surah_number = ?");
$stmt->bind_param("i", $surah);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || !$res->num_rows) fail(404, "Surah not found");
$surahRow = $res->fetch_assoc();
$stmt->close();

// --- Fetch Ayahs ---
$includeTafsir = isset($_GET['include']) && $_GET['include'] === "tafsir";

$fields = "ayah, quran_text, maranao_translation";
if ($includeTafsir) $fields .= ", tafsir_text_original";

$sql = "SELECT $fields FROM quran_tafsir WHERE surah = ? ORDER BY ayah ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $surah);
$stmt->execute();
$ayahsRes = $stmt->get_result();

$ayahs = [];
while ($row = $ayahsRes->fetch_assoc()) {
    $ayahs[] = [
        "ayah_number" => intval($row['ayah']),
        "text_ar" => $row['quran_text'],
        "text_mn" => $row['maranao_translation'],
        "tafsir" => $includeTafsir ? $row['tafsir_text_original'] : null
    ];
}
$stmt->close();

// --- Response ---
ok([
    "surah_number"  => $surah,
    "surah_name_en" => $surahRow['surah_name_en'],
    "surah_name_ar" => $surahRow['surah_name_ar'],
    "ayah_count"    => intval($surahRow['ayah_count']),
    "ayahs"         => $ayahs
]);
