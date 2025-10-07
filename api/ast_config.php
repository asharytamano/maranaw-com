<?php
// Timezone
date_default_timezone_set('Asia/Manila');

// ====== BASIC SETTINGS ======
return [
  // Where to store the daily selection log
  'log_file' => __DIR__ . '/data/ast_log.json',

  // Optional: if you already have a compact index of verses to choose from.
  // If null, we will use your existing API to fetch candidates (see ast_common).
  'ayat_index' => __DIR__ . '/data/ayat_index.json', // or null

  // Define what “short” means (Arabic char length threshold)
  'max_arabic_length' => 140,

  // Build deep links to your site (adjust to your routing)
  // Example uses query: https://maranaw.com/?surah=2&ayah=255
  'permalink_builder' => function(int $surah, int $ayah): string {
    return 'https://maranaw.com/?surah=' . $surah . '&ayah=' . $ayah;
  },

  // Email settings
  'email' => [
    'to'      => 'ashtamano@yahoo.com',
    'from'    => 'no-reply@maranaw.com',
    'fromName'=> 'Ayah for Spiritual Tranquility',
    // If you have SMTP, fill these; otherwise set 'use_smtp' => false to use PHP mail()
    'use_smtp' => false,
    'smtp' => [
      'host' => 'smtp.yourhost.com',
      'port' => 587,
      'secure' => 'tls',         // 'ssl' or 'tls'
      'username' => 'smtp-user',
      'password' => 'smtp-pass',
    ],
  ],

  // Your existing internal API base (adjust to your repo endpoints)
  // We’ll try to fetch Arabic (with Tashkeel) + Maranao by surah/ayah when needed.
  'internal_api' => [
    // Example patterns — change to your actual endpoints:
    // e.g., /api/verse.php?surah=2&ayah=255
    'verse_endpoint' => 'https://maranaw.com/api/verse.php',
    // If you have a “random short ayat” endpoint, put it here and we’ll call that instead:
    'random_short_endpoint' => null,
  ],
];
