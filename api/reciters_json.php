<?php
header('Content-Type: application/json');

echo json_encode([
    "ok" => true,
    "reciters" => [
        [
            "id" => "sudais",
            "name" => "Abdurrahman As-Sudais",
            "base_url" => "https://verses.quran.com/Sudais/mp3",
            "pattern" => "{surah}{ayah}.mp3"
        ],
        [
            "id" => "afasy",
            "name" => "Mishary Rashid Alafasy",
            "base_url" => "https://verses.quran.com/Alafasy/mp3",
            "pattern" => "{surah}{ayah}.mp3"
        ],
        [
            "id" => "ghamdi",
            "name" => "Saad Al-Ghamdi",
            "base_url" => "https://everyayah.com/data/Ghamadi_40kbps",
            "pattern" => "{surah}{ayah}.mp3"
        ],
        [
            "id" => "rifai",
            "name" => "Hani Ar-Rifai",
            "base_url" => "https://everyayah.com/data/Hani_Rifai_192kbps",
            "pattern" => "{surah}{ayah}.mp3"
        ]
    ]
]);
