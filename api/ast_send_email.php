<?php
require __DIR__ . '/ast_common.php';
$config = require __DIR__ . '/ast_config.php';

$today = date('Y-m-d');
$daily = ast_get_daily_pick($today);
if (!$daily) {
  error_log('[AST] No daily ayah selected.');
  exit(1);
}

// Email subject & body (plain + HTML)
$subject = sprintf('Ayah for Spiritual Tranquility — Surah %d:%d (%s)', $daily['surah'], $daily['ayah'], $today);

$plain = "آية اليوم\n\n"
       . $daily['arabic'] . "\n\n"
       . $daily['maranao'] . "\n\n"
       . "Link: " . $daily['permalink'] . "\n";

$html = '<div style="font-family:Segoe UI, Arial, sans-serif; max-width:640px;">'
      . '<h2 style="margin:0 0 8px 0;">Ayah for Spiritual Tranquility</h2>'
      . '<div style="font-size:24px; line-height:1.8; direction:rtl; text-align:right;">'
      . htmlspecialchars($daily['arabic'], ENT_QUOTES, 'UTF-8')
      . '</div>'
      . '<div style="margin-top:10px; font-size:16px;">'
      . nl2br(htmlspecialchars($daily['maranao'], ENT_QUOTES, 'UTF-8'))
      . '</div>'
      . '<p style="margin-top:12px;"><a href="'.htmlspecialchars($daily['permalink']).'">Open on maranaw.com (Surah '
      . (int)$daily['surah'].':'.(int)$daily['ayah'] . ')</a></p>'
      . '<p style="font-size:12px;color:#666;">Date: '.htmlspecialchars($today).'</p>'
      . '</div>';

// ===== SEND EMAIL =====
$to = $config['email']['to'];
$from = $config['email']['from'];
$fromName = $config['email']['fromName'];

if ($config['email']['use_smtp']) {
  // Use PHPMailer via Composer autoload if available
  // require_once __DIR__ . '/vendor/autoload.php';
  // $mail = new PHPMailer\PHPMailer\PHPMailer(true);
  // try { ... } catch (\Throwable $e) { error_log($e->getMessage()); }
  // --- For brevity, not expanding the full PHPMailer block here; say the word and I’ll drop it in. ---
  // TEMP fallback:
  ast_mail_fallback($to,$subject,$plain,$from);
} else {
  // Basic PHP mail() with MIME boundary (works on many hosts)
  ast_mail_basic($to,$subject,$plain,$html,$from,$fromName);
}

function ast_mail_basic($to,$subject,$plain,$html,$from,$fromName){
  $boundary = uniqid('ast_');
  $headers  = "From: {$fromName} <{$from}>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

  $body  = "--{$boundary}\r\n";
  $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
  $body .= $plain . "\r\n";
  $body .= "--{$boundary}\r\n";
  $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
  $body .= $html . "\r\n";
  $body .= "--{$boundary}--";

  @mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, $headers);
}

function ast_mail_fallback($to,$subject,$plain,$from){
  // Minimal fallback if SMTP/PHPMailer not yet wired
  $headers = "From: {$from}\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  @mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $plain, $headers);
}
