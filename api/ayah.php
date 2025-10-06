<?php
include '../config.php';

header("Content-Type: application/json");

$surah_number = isset($_GET['surah']) ? intval($_GET['surah']) : 1;
$ayah_number  = isset($_GET['ayah']) ? intval($_GET['ayah']) : 1;

$stmt = $conn->prepare("SELECT surah, ayah, quran_text, maranao_translation, tafsir_text_original 
                        FROM quran_tafsir WHERE surah=? AND ayah=?");
$stmt->bind_param("ii", $surah_number, $ayah_number);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode($row, JSON_UNESCAPED_UNICODE);
