<?php
// ayah-json.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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
$ayah  = isset($_GET['ayah']) ? intval($_GET['ayah']) : 0;

if ($surah < 1 || $surah > 114) fail(400, "Invalid surah number");
if ($ayah < 1) fail(400, "Invalid ayah number");

$includeTafsir = isset($_GET['include']) && $_GET['include'] === "tafsir";

// --- Fetch ayah ---
$fields = "ayah, quran_text, maranao_translation";
if ($includeTafsir) $fields .= ", tafsir_text_original";

$sql = "SELECT $fields FROM quran_tafsir WHERE surah = ? AND ayah = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $surah, $ayah);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || !$res->num_rows) {
    fail(404, "Ayah not found");
}

$row = $res->fetch_assoc();
$stmt->close();

$data = [
    "surah_number" => $surah,
    "ayah_number"  => intval($row['ayah']),
    "text_ar"      => $row['quran_text'],
    "text_mn"      => $row['maranao_translation'],
    "tafsir"       => $includeTafsir ? $row['tafsir_text_original'] : null
];

// --- Response ---
ok($data);
