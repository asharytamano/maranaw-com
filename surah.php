<?php
include 'config.php';

// Get Surah number from URL
$surah_number = isset($_GET['surah']) ? intval($_GET['surah']) : 1;

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ayahs_per_page = 20;
$offset = ($page - 1) * $ayahs_per_page;

// Fetch Surah details
$stmt = $conn->prepare("SELECT surah_name_en, surah_name_ar, ayah_count FROM surahs WHERE surah_number = ?");
$stmt->bind_param("i", $surah_number);
$stmt->execute();
$stmt->bind_result($surah_name_en, $surah_name_ar, $ayah_count);
$stmt->fetch();
$stmt->close();

// Null-safe check
if (!$surah_name_en) {
    echo "<p>Surah not found.</p>";
    exit;
}

// Bismillah
$bismillah_text = '';
$show_bismillah = ($surah_number != 9 && $surah_number != 1);
if($show_bismillah){
    $stmt = $conn->prepare("SELECT quran_text FROM quran_tafsir WHERE surah = ? AND ayah = 0");
    $stmt->bind_param("i", $surah_number);
    $stmt->execute();
    $stmt->bind_result($bismillah_text);
    $stmt->fetch();
    $stmt->close();
}

// Fetch ayahs
$stmt = $conn->prepare("SELECT ayah, quran_text, maranao_translation, tafsir_text_original 
                        FROM quran_tafsir 
                        WHERE surah = ? AND ayah > 0
                        ORDER BY ayah ASC 
                        LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $surah_number, $ayahs_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Surah <?php echo $surah_number; ?> - Maranaw Tafsir</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="surah.css">
<link rel="icon" type="image/png" href="images/favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Scheherazade+New&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>
<hr>

<main class="site-content">

<div class="surah-title">
    <?php echo $surah_number . ". " . htmlspecialchars($surah_name_en ?? '') . " | "; ?>
    <span class="arabic"><?php echo htmlspecialchars($surah_name_ar ?? ''); ?></span>
</div>

<!-- 3 Toggle Buttons + Reciter Selector + Switches -->
<div class="sticky-controls">
  <div class="toggle-group">
      <button id="btn-arabic">Arabic Only</button>
      <button id="btn-maranao">Maranao Only</button>
      <button id="btn-both" class="active">Arabic + Maranao</button>
  </div>

  <!-- Reciter Selector -->
<div style="text-align:center; margin-top:10px;">
    <label for="reciter" style="font-weight:bold;">Reciter: </label>
    <select id="reciter" onchange="changeReciter(this.value)">
        <option value="Alafasy" selected>Mishary Rashid Al-Afasy</option>
        <option value="Sudais">Abdul Rahman Al-Sudais</option>
        <option value="Ghamdi">Saad al-Ghamdi (40kbps)</option>
        <option value="Rifai">Hani ar-Rifai (192kbps)</option>
    </select>
</div>

  <!-- Auto-scroll toggle -->
<div class="toggles-row">
  <label class="switch">
    <input type="checkbox" id="autoScrollToggle" checked>
    <span class="slider round"></span>
  </label>
  <span class="toggle-label">🔄 Auto-scroll</span>

  <label class="switch" style="margin-left:20px;">
    <input type="checkbox" id="continuousPlayToggle" checked>
    <span class="slider round"></span>
  </label>
  <span class="toggle-label">▶️ Continuous Play</span>
</div>

  <!-- View Favorites link -->
  <div style="text-align:center; margin-top:10px;">
      <a href="favorites.php" style="font-size:14px; color:#0066cc; text-decoration:none;">
          <strong>🖤 VIEW FAVORITES</strong>
      </a>
  </div>
</div>

<!-- Bismillah -->
<?php if($show_bismillah && $bismillah_text): ?>
    <div class="ayah-block bismillah-block">
        <div class="quran-text"><?php echo $bismillah_text; ?></div>
    </div>
<?php endif; ?>

<!-- Ayahs -->
<?php while($row = $result->fetch_assoc()): ?>
    <?php
        $ayah = $row['ayah'];
        $audio_url = "https://verses.quran.com/Alafasy/mp3/" 
                   . str_pad($surah_number, 3, "0", STR_PAD_LEFT) 
                   . str_pad($ayah, 3, "0", STR_PAD_LEFT) 
                   . ".mp3";
        $ayah_key = $surah_number . ":" . $ayah;
    ?>
    <div class="ayah-block" id="ayah-<?php echo $ayah; ?>">
        <div class="quran-text"><?php echo $row['quran_text']; ?> 
            <span class="ayah-number">(<?php echo $ayah; ?>)</span>
        </div>
        <div class="translation"><?php echo $row['maranao_translation']; ?></div>

        <div class="ayah-actions">
            <!-- Enhanced Audio Player -->
            <audio id="audio-<?php echo $ayah_key; ?>" preload="none"
                   data-surah="<?php echo $surah_number; ?>"
                   data-ayah="<?php echo $ayah; ?>"
                   data-reciter="Alafasy"
                   ontimeupdate="updateProgress('<?php echo $ayah_key; ?>')"
                   onplay="highlightAyah('<?php echo $ayah; ?>')"
                   onended="removeHighlight('<?php echo $ayah; ?>')">
                <source src="<?php echo $audio_url; ?>" type="audio/mp3">
            </audio>

            <button id="btn-play-<?php echo $ayah_key; ?>" onclick="togglePlay('<?php echo $ayah_key; ?>')">▶️ Play</button>
            <button onclick="stopAudio('audio-<?php echo $ayah_key; ?>')">⏹ Stop</button>
            
<!-- Progress bar + Time Display -->
<div style="display:inline-block; vertical-align:middle;">
  <input type="range" id="progress-<?php echo $ayah_key; ?>" value="0" max="100"
         onchange="seekAudio('<?php echo $ayah_key; ?>', this.value)" style="width:200px;">
  <span id="time-<?php echo $ayah_key; ?>" class="time-display">0:00 / 0:00</span>
</div>
            
            <!-- Volume -->
            <input type="range" id="volume-<?php echo $ayah_key; ?>" min="0" max="1" step="0.05" value="1"
                   onchange="setVolume('<?php echo $ayah_key; ?>', this.value)">
        </div>

        <div class="ayah-actions">
            <!-- Existing Buttons -->
            <button onclick="toggleTafsir(<?php echo $ayah; ?>)">Show Tafsir</button>
            <button onclick="toggleFavorite(<?php echo $ayah; ?>)">🖤 Add to Favorites</button>
            <button onclick="copyAyah(<?php echo $ayah; ?>)">📋 Copy</button>
            <button class="share-btn"
                onclick="shareAyah('<?php echo $surah_number; ?>',
                                   '<?php echo $ayah; ?>',
                                   '<?php echo addslashes($row['maranao_translation']); ?>')">
                Share
            </button>
        </div>

        <div id="tafsir-<?php echo $ayah; ?>" class="tafsir"><?php echo $row['tafsir_text_original']; ?></div>
        <div id="share-links-<?php echo $ayah; ?>" class="share-links" style="display:none;"></div>
    </div>
<?php endwhile; ?>

<!-- Pagination -->
<?php
$total_pages = ceil($ayah_count / $ayahs_per_page);
if($total_pages > 1):
?>
<div class="pagination">
    <?php
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) echo "<strong>$i</strong>";
        else echo "<a href='surah.php?surah=$surah_number&page=$i'>$i</a>";
    }
    if ($page < $total_pages) echo "<a href='surah.php?surah=$surah_number&page=$total_pages'>Last</a>";
    ?>
</div>
<?php endif; ?>

</main>
<hr>
<footer class="site-footer">
    <p style="text-align: center;">© 2025 Maranaw Tafsir by Abu Ahmad Tamano. | Site developed by Ashary Tamano. | All rights reserved.</p>
</footer>

<!-- Scroll to Top -->
<button onclick="scrollToTop()" id="scrollBtn" style="display:none;position:fixed;bottom:40px;right:30px;z-index:99;font-size:18px;border:none;outline:none;background-color:#000;color:white;cursor:pointer;padding:15px;border-radius:50%;">⬆</button>

<script>

// Restore saved reciter on page load
window.addEventListener("DOMContentLoaded", () => {
  const savedReciter = localStorage.getItem("selectedReciter");
  if (savedReciter) {
    document.getElementById("reciter").value = savedReciter;
    changeReciter(savedReciter);
  }
});

// Scroll-to-top
let scrollBtn = document.getElementById("scrollBtn");
window.onscroll = function() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) scrollBtn.style.display = "block";
    else scrollBtn.style.display = "none";
};
function scrollToTop() { window.scrollTo({top: 0, behavior: 'smooth'}); }

// 3-toggle buttons
const btnArabic = document.getElementById("btn-arabic");
const btnMaranao = document.getElementById("btn-maranao");
const btnBoth = document.getElementById("btn-both");

btnArabic.addEventListener('click', () => { setView('arabic'); setActive(btnArabic); });
btnMaranao.addEventListener('click', () => { setView('maranao'); setActive(btnMaranao); });
btnBoth.addEventListener('click', () => { setView('both'); setActive(btnBoth); });

function setView(view){
    const arabicTexts = document.querySelectorAll(".quran-text");
    const maranaoTexts = document.querySelectorAll(".translation");
    if(view==='arabic'){
        arabicTexts.forEach(t=>t.style.display='block');
        maranaoTexts.forEach(t=>t.style.display='none');
    } else if(view==='maranao'){
        arabicTexts.forEach(t=>t.style.display='none');
        maranaoTexts.forEach(t=>t.style.display='block');
    } else {
        arabicTexts.forEach(t=>t.style.display='block');
        maranaoTexts.forEach(t=>t.style.display='block');
    }
}
function setActive(btn){
    [btnArabic, btnMaranao, btnBoth].forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
}
setView('both');

// -------------------- Enhanced Audio Functions --------------------
let currentAudio = null;

function togglePlay(key) {
  const audio = document.getElementById("audio-" + key);
  const btn = document.getElementById("btn-play-" + key);

  if (audio.paused) {
    if (currentAudio && currentAudio !== audio) {
      currentAudio.pause();
      currentAudio.currentTime = 0;
      const oldKey = currentAudio.getAttribute("data-surah") + ":" + currentAudio.getAttribute("data-ayah");
      document.getElementById("btn-play-" + oldKey).innerText = "▶️ Play";
      removeHighlight(currentAudio.getAttribute("data-ayah"));
    }
    audio.play();
    currentAudio = audio;
    btn.innerText = "⏸ Pause";
  } else {
    audio.pause();
    btn.innerText = "▶️ Play";
  }
}

function stopAudio(id) {
  const audio = document.getElementById(id);
  const ayah = audio.getAttribute("data-ayah");
  audio.pause();
  audio.currentTime = 0;
  document.getElementById("btn-play-" + audio.getAttribute("data-surah") + ":" + ayah).innerText = "▶️ Play";
  removeHighlight(ayah);
}

function updateProgress(key) {
  const audio = document.getElementById("audio-" + key);
  const progress = document.getElementById("progress-" + key);
  const timeLabel = document.getElementById("time-" + key);

  if (audio.duration) {
    progress.value = (audio.currentTime / audio.duration) * 100;

    // Format time
    const cur = formatTime(audio.currentTime);
    const dur = formatTime(audio.duration);
    timeLabel.innerText = `${cur} / ${dur}`;
  }
}

function formatTime(sec) {
  const minutes = Math.floor(sec / 60) || 0;
  const seconds = Math.floor(sec % 60) || 0;
  return `${minutes}:${seconds < 10 ? "0" + seconds : seconds}`;
}

function seekAudio(key, value) {
  const audio = document.getElementById("audio-" + key);
  if (audio.duration) {
    audio.currentTime = (value / 100) * audio.duration;
  }
}

function setVolume(key, value) {
  const audio = document.getElementById("audio-" + key);
  audio.volume = value;
}

function highlightAyah(ayah) {
  const block = document.getElementById("ayah-" + ayah);
  block.classList.add("playing");

  // Auto-scroll only if toggle is ON
  const autoScroll = document.getElementById("autoScrollToggle").checked;
  if (autoScroll) {
    setTimeout(() => {
      block.scrollIntoView({ behavior: "smooth", block: "center" });
    }, 150); // 150ms delay to feel natural
  }
}

function removeHighlight(ayah) {
  document.getElementById("ayah-" + ayah).classList.remove("playing");
}

// Continuous play
document.querySelectorAll("audio").forEach((audio, index, list) => {
  audio.onended = function() {
    const continuousPlay = document.getElementById("continuousPlayToggle").checked;
    removeHighlight(audio.getAttribute("data-ayah"));
    if (continuousPlay) {
      let next = list[index + 1];
      if (next) {
        const nextKey = next.getAttribute("data-surah") + ":" + next.getAttribute("data-ayah");
        togglePlay(nextKey);
      }
    }
  };
});

// Change reciter dynamically
function changeReciter(reciter) {
  if (currentAudio) {
    currentAudio.pause();
    currentAudio.currentTime = 0;
    currentAudio = null;
  }

  // Save chosen reciter
  localStorage.setItem("selectedReciter", reciter);

  document.querySelectorAll("audio").forEach(audio => {
    let surah = audio.getAttribute("data-surah").padStart(3, "0");
    let ayah = audio.getAttribute("data-ayah").padStart(3, "0");
    let newUrl = "";

    if (reciter === "Alafasy") {
      newUrl = `https://verses.quran.com/Alafasy/mp3/${surah}${ayah}.mp3`;
    } else if (reciter === "Sudais") {
      newUrl = `https://verses.quran.com/Sudais/mp3/${surah}${ayah}.mp3`;
    } else if (reciter === "Ghamdi") {
      newUrl = `https://everyayah.com/data/Ghamadi_40kbps/${surah}${ayah}.mp3`;
    } else if (reciter === "Rifai") {
      newUrl = `https://everyayah.com/data/Hani_Rifai_192kbps/${surah}${ayah}.mp3`;
    }

    audio.querySelector("source").src = newUrl;
    audio.setAttribute("data-reciter", reciter);
    audio.load();
  });
}

// -------------------- Existing Functions --------------------
function toggleTafsir(id) {
    const tafsirDiv = document.getElementById("tafsir-" + id);
    const button = tafsirDiv.previousElementSibling.querySelector("button");

    if (tafsirDiv.style.display === "none" || tafsirDiv.style.display === "") {
        tafsirDiv.style.display = "block";
        button.innerText = "Hide Tafsir";
    } else {
        tafsirDiv.style.display = "none";
        button.innerText = "Show Tafsir";
    }
}

function toggleFavorite(id) {
    const ayahDiv = document.getElementById("ayah-" + id);
    const surah = "<?php echo $surah_number . '. ' . addslashes($surah_name_en); ?> (<?php echo addslashes($surah_name_ar); ?>) - Ayah " + id;
    const quran = ayahDiv.querySelector(".quran-text").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;

    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    const exists = favorites.find(f => f.surah === surah && f.quran === quran);

    if (exists) {
        favorites = favorites.filter(f => !(f.surah === surah && f.quran === quran));
        localStorage.setItem("favorites", JSON.stringify(favorites));
        alert("Removed from Favorites");
    } else {
        favorites.push({ id, surah, quran, translation });
        localStorage.setItem("favorites", JSON.stringify(favorites));
        alert("Added to Favorites.\n\nClick 🖤 View Favorites above to see your saved ayahs.");
    }
}

function copyAyah(id) {
    const ayahDiv = document.getElementById("ayah-" + id);
    const surah = "<?php echo $surah_number . '. ' . addslashes($surah_name_en); ?> (<?php echo addslashes($surah_name_ar); ?>) - Ayah " + id;
    const quran = ayahDiv.querySelector(".quran-text").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;
    const tafsir = document.getElementById("tafsir-" + id).innerText;

    const text = surah + "\n" + quran + "\n" + translation + "\n" + tafsir;
    navigator.clipboard.writeText(text);
    alert("Copied to clipboard");
}

function shareAyah(id) {
    const ayahDiv = document.getElementById("ayah-" + id);
    const surah = "<?php echo $surah_number . '. ' . addslashes($surah_name_en); ?> (<?php echo addslashes($surah_name_ar); ?>) - Ayah " + id;
    const quran = ayahDiv.querySelector(".quran-text").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;

    const text = surah + "\n" + quran + "\n" + translation;

    if (navigator.share) {
        navigator.share({ text })
            .then(() => console.log("Shared successfully"))
            .catch(err => console.error("Error sharing:", err));
    } else {
        const shareLinks = document.getElementById("share-links-" + id);
        const encoded = encodeURIComponent(text);
        shareLinks.innerHTML = `
            <a href="https://www.facebook.com/sharer/sharer.php?u=${encoded}" target="_blank">Facebook</a>
            <a href="https://twitter.com/intent/tweet?text=${encoded}" target="_blank">X</a>
            <a href="https://wa.me/?text=${encoded}" target="_blank">WhatsApp</a>
        `;
        shareLinks.style.display = "block";
    }
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
