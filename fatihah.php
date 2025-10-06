<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Surah Al-Fatiha – Audio Test</title>
<style>
body {
  font-family: Arial, sans-serif;
  line-height: 1.6;
  max-width: 800px;
  margin: 20px auto;
  padding: 10px;
}
.ayah {
  border-bottom: 1px solid #ddd;
  padding: 10px 0;
}
.btn {
  background: #008CBA;
  color: #fff;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-right: 5px;
}
.btn:hover { background: #005f73; }
</style>
</head>
<body>

<h2>سورة الفاتحة</h2>
<h3>Surah Al-Fātiḥah (The Opening)</h3>

<?php
$ayahs = [
  1 => ["arabic" => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ", "maranao" => "Sa ngaran o Allah, a Makaompon o Mapangingkaba."],
  2 => ["arabic" => "الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ", "maranao" => "Mithankirog so Allāh, so Gnoron o manga katao."],
  3 => ["arabic" => "الرَّحْمَٰنِ الرَّحِيمِ", "maranao" => "So Mapangingkaba o Makaompon."],
  4 => ["arabic" => "مَالِكِ يَوْمِ الدِّينِ", "maranao" => "So Poon sa araw o pagkakalkal."],
  5 => ["arabic" => "إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ", "maranao" => "So Ikka tanu iayad, so Ikka tanu iankirog sa tabang."],
  6 => ["arabic" => "اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ", "maranao" => "Pangimomo kami sa dalan a matu."],
  7 => ["arabic" => "صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ", "maranao" => "So dalan o mga tinaron-an Nya, aba skaniyan a di miyatap, ago da skaniyan a da giya."]
];

foreach ($ayahs as $num => $text) {
    $audio_url = "https://verses.quran.com/Alafasy/mp3/" 
               . str_pad(1, 3, "0", STR_PAD_LEFT) 
               . str_pad($num, 3, "0", STR_PAD_LEFT) 
               . ".mp3";
    $ayah_key = "001:" . $num;
    echo "<div class='ayah'>
            <p><strong>{$text['arabic']}</strong></p>
            <p><em>{$text['maranao']}</em></p>
            <audio id='audio-{$ayah_key}' preload='none'>
                <source src='{$audio_url}' type='audio/mp3'>
            </audio>
            <button class='btn' onclick=\"playAudio('audio-{$ayah_key}')\">▶️ Play</button>
            <button class='btn' onclick=\"pauseAudio('audio-{$ayah_key}')\">⏸ Pause</button>
            <button class='btn' onclick=\"stopAudio('audio-{$ayah_key}')\">⏹ Stop</button>
          </div>";
}
?>

<script>
let currentAudio = null;

// Play function
function playAudio(id) {
  const audio = document.getElementById(id);

  // stop currently playing
  if (currentAudio && currentAudio !== audio) {
    currentAudio.pause();
    currentAudio.currentTime = 0;
  }

  audio.play();
  currentAudio = audio;
}

// Pause
function pauseAudio(id) {
  const audio = document.getElementById(id);
  if (!audio.paused) audio.pause();
}

// Stop
function stopAudio(id) {
  const audio = document.getElementById(id);
  audio.pause();
  audio.currentTime = 0;
}

// Continuous play handler
document.querySelectorAll("audio").forEach((audio, index, list) => {
  audio.onended = function() {
    let next = list[index + 1];
    if (next) {
      next.play();
      currentAudio = next;
    }
  };
});
</script>

</body>
</html>
