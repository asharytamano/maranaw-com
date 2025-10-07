<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/ast_common.php';

// If you call with ?date=YYYY-MM-DD you can preview a date; else today.
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$daily = ast_get_daily_pick($date);

if (!$daily) {
  http_response_code(503);
  echo json_encode(['error' => 'No ayah available today.']);
  exit;
}

echo json_encode([
  'date'     => $daily['date'],
  'surah'    => $daily['surah'],
  'ayah'     => $daily['ayah'],
  'arabic'   => $daily['arabic'],
  'maranao'  => $daily['maranao'],
  'permalink'=> $daily['permalink'],
], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
