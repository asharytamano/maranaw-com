<?php
include '../config.php';

header("Content-Type: application/json");

$surah_number = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Surah info
$stmt = $conn->prepare("SELECT surah_number, surah_name_en, surah_name_ar, ayah_count FROM surahs WHERE surah_number=?");
$stmt->bind_param("i", $surah_number);
$stmt->execute();
$surah = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ayahs
$stmt = $conn->prepare("SELECT ayah, quran_text, maranao_translation, tafsir_text_original 
                        FROM quran_tafsir WHERE surah=? ORDER BY ayah ASC");
$stmt->bind_param("i", $surah_number);
$stmt->execute();
$result = $stmt->get_result();

$ayahs = [];
while($row = $result->fetch_assoc()) {
    $ayahs[] = $row;
}
$stmt->close();

$surah['ayahs'] = $ayahs;
echo json_encode($surah, JSON_UNESCAPED_UNICODE);
