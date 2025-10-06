<?php
// search-json.php
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
    echo json_encode(["ok"=>true, "results"=>$data], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- Validate input ---
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') fail(400, "Missing search query");

$includeTafsir = isset($_GET['include']) && $_GET['include'] === "tafsir";

// --- Prepare LIKE pattern ---
$like = "%" . $conn->real_escape_string($q) . "%";

// --- Build SQL ---
$fields = "surah, ayah, quran_text, maranao_translation";
if ($includeTafsir) $fields .= ", tafsir_text_original";

$sql = "SELECT $fields
        FROM quran_tafsir
        WHERE quran_text LIKE ? OR maranao_translation LIKE ?"
        . ($includeTafsir ? " OR tafsir_text_original LIKE ?" : "")
        . " ORDER BY surah, ayah LIMIT 100"; // limit results

$stmt = $conn->prepare($sql);

if ($includeTafsir) {
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt->bind_param("ss", $like, $like);
}

$stmt->execute();
$res = $stmt->get_result();

$results = [];
while ($row = $res->fetch_assoc()) {
    $results[] = [
        "surah_number" => intval($row['surah']),
        "ayah_number"  => intval($row['ayah']),
        "text_ar"      => $row['quran_text'],
        "text_mn"      => $row['maranao_translation'],
        "tafsir"       => $includeTafsir ? $row['tafsir_text_original'] : null
    ];
}
$stmt->close();

// --- Response ---
ok($results);
