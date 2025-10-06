<?php
include 'config.php';

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo "<p>Please enter a search term.</p>";
    exit;
}

$query = trim($_GET['q']);
$results = [];

// Check for surah:ayah formats (2:245, 2 245, Al-Baqarah:245)
$pattern1 = '/^(\d+)[\s:]?(\d+)$/';           // numeric: 2:245 or 2 245
$pattern2 = '/^([a-zA-Z-]+)[\s:]?(\d+)$/';    // surah name: Al-Baqarah:245

if (preg_match($pattern1, $query, $matches)) {
    $surah_number = (int)$matches[1];
    $ayah_number   = (int)$matches[2];
    $stmt = $conn->prepare("SELECT * FROM quran_tafsir WHERE surah = ? AND ayah = ?");
    $stmt->bind_param("ii", $surah_number, $ayah_number);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} elseif (preg_match($pattern2, $query, $matches)) {
    $surah_name = $matches[1];
    $ayah_number = (int)$matches[2];

    // First get surah number
    $stmt = $conn->prepare("SELECT surah_number FROM surahs WHERE surah_name_en LIKE ?");
    $likeName = "%$surah_name%";
    $stmt->bind_param("s", $likeName);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $surah_number = $row['surah_number'];
        $stmt2 = $conn->prepare("SELECT * FROM quran_tafsir WHERE surah = ? AND ayah = ?");
        $stmt2->bind_param("ii", $surah_number, $ayah_number);
        $stmt2->execute();
        $results = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Keyword search in Maranao translation or tafsir
    $keyword = "%$query%";
    $stmt = $conn->prepare("SELECT * FROM quran_tafsir WHERE maranao_translation LIKE ? OR tafsir_text_original LIKE ?");
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Display results
if (empty($results)) {
    echo "<p>No results found for <strong>" . htmlspecialchars($query) . "</strong>.</p>";
} else {
    foreach ($results as $row) {
        echo '<div class="ayah-block" style="max-width:800px; margin:15px auto; padding:12px; border-bottom:1px solid #ccc;">';
        echo '<div class="quran-text" style="font-family:Amiri, serif; font-size:22px;">' 
             . htmlspecialchars($row['quran_text']) 
             . ' <span class="ayah-number">' . htmlspecialchars($row['ayah']) . '</span></div>';
        echo '<div class="translation" style="font-family:Merriweather, serif; font-size:18px; margin-top:8px;">' 
             . htmlspecialchars($row['maranao_translation']) . '</div>';
        echo '<div class="tafsir" style="display:none; font-family:Merriweather, serif; font-size:16px; margin-top:8px; color:#555;">' 
             . htmlspecialchars($row['tafsir_text_original']) . '</div>';
        echo '<div style="text-align:center;"><button class="toggle-btn" style="display:inline-block; margin-top:5px; padding:5px 10px; font-size:14px; background-color:#000; color:#fff; cursor:pointer; border-radius:5px;">Show Tafsir</button></div>';
        echo '</div>';
    }
}
?>

<script>
// Toggle tafsir for results
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let tafsirDiv = this.parentElement.previousElementSibling;
        if (tafsirDiv.style.display === 'none' || tafsirDiv.style.display === '') {
            tafsirDiv.style.display = 'block';
            this.textContent = 'Hide Tafsir';
        } else {
            tafsirDiv.style.display = 'none';
            this.textContent = 'Show Tafsir';
        }
    });
});
</script>
