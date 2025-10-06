<?php include 'header.php'; ?>
<hr>

<div class="site-content" style="max-width: 1200px; margin: 0 auto;">

  <h1 style="text-align:center;">📖 Search the Maranaw Tafsir</h1>

  <!-- 🔎 Search bar -->
  <div class="search-box">
    <input id="search-input" 
           type="text" 
           placeholder="Search keyword (e.g., iman, sabr)" 
           style="padding:6px; width:250px;"
           onkeypress="if(event.key === 'Enter'){ searchAyahs(); }">

    <button onclick="searchAyahs()" style="padding:6px 12px;">Search</button>
    <button onclick="loadSurahs()" style="padding:6px 12px;">Clear</button>
    <label style="margin-left:10px;">
      <input type="checkbox" id="exact-match"> Exact Match Only
    </label>

<!-- ⭐ Favorites link -->
<a href="favorites.php" style="margin-left:15px; font-size:14px; color:#0066cc; text-decoration:none;">
  ⭐ View Favorites
</a>


    <div class="search-tips">
      🔎 Search Tips:<br>
      - Use multiple keywords (e.g., iman sabr)<br>
      - Case insensitive (Iman = iman)<br>
      - Works on Qur’an text, Maranao translation, and Tafsir<br>
      - Check "Exact Match Only" to match whole words only (e.g., iman but not satiman)
    </div>
  </div>

  <!-- Wrapper -->
  <div class="search-wrapper">
    <div class="surah-list" id="surah-list">
      <h3>Surahs</h3>
      <div>Loading surahs...</div>
    </div>

    <div class="tafsir-display">
      <h3 id="surah-title">Select a Surah or Search</h3>
      <div id="ayahs"></div>
      <div id="pagination" class="pagination"></div>
    </div>
  </div>

</div>

<hr>
<?php include 'footer.php'; ?>

<!-- Fonts + CSS -->
<link href="https://fonts.googleapis.com/css2?family=Amiri&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

<style>
  /* Flex wrapper */
  .search-wrapper {
    display: flex;
    flex-direction: row;
    gap: 20px;
  }

  .surah-list { flex: 0 0 280px; }
  .tafsir-display { flex: 1; }

  .surah-item { padding: 8px; cursor: pointer; border-bottom: 1px solid #ddd; }
  .surah-item:hover { background: #f0f0f0; }
  .ayah { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #eee; }

  /* Arabic Qur'an text */
  .quran {
    font-family: 'Amiri', serif;
    font-size: 22px;
    line-height: 2;
    color: darkgreen;
    direction: rtl;
    text-align: right;
  }

  /* Arabic Surah name in sidebar */
  .surah-arabic {
    font-family: 'Amiri', serif;
    font-size: 18px;
    direction: rtl;
    text-align: right;
    display: block;
    color: #333;
    transition: color 0.3s, font-weight 0.3s;
  }
  .surah-item:hover .surah-arabic {
    font-weight: bold;
    color: green;
  }
  .surah-item.active .surah-arabic {
    font-weight: bold;
    color: green;
  }

  /* Translation */
  .translation {
    font-family: 'Merriweather', serif;
    font-size: 16px;
    line-height: 1.8;
    color: darkblue;
    margin-top: 5px;
    text-align: left;
  }

  /* Tafsir */
  .tafsir {
    font-family: 'Merriweather', serif;
    font-size: 15px;
    line-height: 1.8;
    margin-top: 5px;
    font-style: italic;
    display: none;
    text-align: justify;
    color: #333;
  }

  .ayah-actions {
    margin-top: 6px;
  }
  .ayah-actions button {
    margin-right: 6px;
    padding: 4px 10px;
    font-size: 14px;
    cursor: pointer;
  }

  .share-links {
    margin-top: 6px;
    font-size: 14px;
  }
  .share-links a {
    margin-right: 8px;
    text-decoration: none;
    color: #0066cc;
  }

  .search-box { margin-bottom: 10px; position: sticky; top: 0; background: #fff; padding: 10px; z-index: 10; border-bottom: 1px solid #ddd; }
  .pagination { margin-top: 15px; text-align: center; }
  .pagination button { margin: 0 3px; padding: 6px 12px; }
  mark { background: yellow; font-weight: bold; }
  .search-tips { font-size: 13px; color: #555; margin-top: 5px; }

  /* 📱 Responsive */
  @media (max-width: 768px) {
    .search-wrapper { flex-direction: column; }
    .surah-list { flex: 1; max-width: 100%; order: 1; }
    .tafsir-display { order: 2; }
  }
</style>

<script>
  let currentResults = [];
  let currentPage = 1;
  const perPage = 20;

  function renderAyahs() {
    const ayahsDiv = document.getElementById("ayahs");
    ayahsDiv.innerHTML = "";

    if (currentResults.length === 0) {
      ayahsDiv.innerHTML = "<p>No results found.</p>";
      document.getElementById("pagination").innerHTML = "";
      return;
    }

    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageItems = currentResults.slice(start, end);

    pageItems.forEach((r, index) => {
      const id = index + start;
      const div = document.createElement("div");
      div.className = "ayah";
      div.id = "ayah-" + id;

      div.innerHTML = `
        <div><strong>Surah ${r.surah}, Ayah ${r.ayah}</strong></div>
        <div class="quran">${r.quran_text}</div>
        <div class="translation">🔹 ${r.maranao_translation}</div>

        <div class="ayah-actions">
          <button onclick="toggleTafsir(${id})">Show Tafsir</button>
          <button onclick="toggleFavorite(${id})">⭐ Add to Favorites</button>
          <button onclick="copyAyah(${id})">📋 Copy</button>
          <button class="share-btn"
    onclick="shareAyah('<?php echo $row['surah']; ?>',
                       '<?php echo $row['ayah']; ?>',
                       '<?php echo addslashes($row['maranao_translation']); ?>')">
    Share
</button>
        </div>

        <div id="tafsir-${id}" class="tafsir">📖 ${r.tafsir_text_original}</div>
        <div id="share-links-${id}" class="share-links" style="display:none;"></div>
      `;
      ayahsDiv.appendChild(div);
    });

    renderPagination();
  }

  function renderPagination() {
    const totalPages = Math.ceil(currentResults.length / perPage);
    const pagDiv = document.getElementById("pagination");
    pagDiv.innerHTML = "";

    if (totalPages <= 1) return;

    if (currentPage > 1) {
      const prevBtn = document.createElement("button");
      prevBtn.innerText = "Prev";
      prevBtn.onclick = () => { currentPage--; renderAyahs(); };
      pagDiv.appendChild(prevBtn);
    }

    if (currentPage > 3) {
      addPageButton(1);
      if (currentPage > 4) {
        const span = document.createElement("span");
        span.innerText = "...";
        pagDiv.appendChild(span);
      }
    }

    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
      addPageButton(i);
    }

    if (currentPage < totalPages - 2) {
      if (currentPage < totalPages - 3) {
        const span = document.createElement("span");
        span.innerText = "...";
        pagDiv.appendChild(span);
      }
      addPageButton(totalPages);
    }

    if (currentPage < totalPages) {
      const nextBtn = document.createElement("button");
      nextBtn.innerText = "Next";
      nextBtn.onclick = () => { currentPage++; renderAyahs(); };
      pagDiv.appendChild(nextBtn);
    }

    function addPageButton(i) {
      const pageBtn = document.createElement("button");
      pageBtn.innerText = i;
      if (i === currentPage) {
        pageBtn.style.fontWeight = "bold";
        pageBtn.style.background = "#ddd";
      }
      pageBtn.onclick = () => { currentPage = i; renderAyahs(); };
      pagDiv.appendChild(pageBtn);
    }
  }

  async function loadSurahs() {
    const res = await fetch("api/surahs.php");
    const surahs = await res.json();

    const list = document.getElementById("surah-list");
    list.innerHTML = "<h3>Surahs</h3>";

    surahs.forEach(s => {
      const div = document.createElement("div");
      div.className = "surah-item";
      div.innerHTML = `
        ${s.surah_number}. ${s.surah_name_en}
        <span class="surah-arabic">${s.surah_name_ar}</span>
      `;
      div.onclick = () => {
        document.querySelectorAll(".surah-item").forEach(item => item.classList.remove("active"));
        div.classList.add("active");
        loadSurah(s.surah_number, s.surah_name_en, s.surah_name_ar);
      };
      list.appendChild(div);
    });

    document.getElementById("surah-title").innerText = "Select a Surah or Search";
    document.getElementById("ayahs").innerHTML = "";
    document.getElementById("pagination").innerHTML = "";
  }

  async function loadSurah(id, nameEn, nameAr) {
    const res = await fetch("api/surah.php?id=" + id);
    const surah = await res.json();

    document.getElementById("surah-title").innerText =
      surah.surah_number + ". " + nameEn + " (" + nameAr + ")";

    currentResults = surah.ayahs;
    currentPage = 1;
    renderAyahs();
  }

  async function searchAyahs() {
    const q = document.getElementById("search-input").value.trim();
    const exact = document.getElementById("exact-match").checked ? 1 : 0;

    if (!q) {
      alert("Enter a keyword to search");
      return;
    }

    document.getElementById("surah-title").innerText = "Search results for: " + q;
    document.getElementById("ayahs").innerHTML = "Searching...";

    const res = await fetch("api/search.php?q=" + encodeURIComponent(q) + "&exact=" + exact);
    let results = await res.json();

    const keywords = q.split(/\s+/).filter(k => k.length > 0);
    const regex = new RegExp("(" + keywords.join("|") + ")", "gi");
    results = results.map(r => ({
      ...r,
      quran_text: r.quran_text.replace(regex, "<mark>$1</mark>"),
      maranao_translation: r.maranao_translation.replace(regex, "<mark>$1</mark>"),
      tafsir_text_original: r.tafsir_text_original.replace(regex, "<mark>$1</mark>")
    }));

    currentResults = results;
    currentPage = 1;
    renderAyahs();
    document.querySelectorAll(".surah-item").forEach(item => item.classList.remove("active"));
  }

  function toggleTafsir(index) {
    const tafsirDiv = document.getElementById("tafsir-" + index);
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
    const surah = ayahDiv.querySelector("strong").innerText;
    const quran = ayahDiv.querySelector(".quran").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;

    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");

    const exists = favorites.find(f => f.id === id);
    if (exists) {
      favorites = favorites.filter(f => f.id !== id);
      localStorage.setItem("favorites", JSON.stringify(favorites));
      alert("Removed from Favorites");
      ayahDiv.querySelector(".ayah-actions button:nth-child(2)").innerText = "⭐ Add to Favorites";
    } else {
      favorites.push({ id, surah, quran, translation });
      localStorage.setItem("favorites", JSON.stringify(favorites));
      alert("Added to Favorites.\n\nClick ⭐ View Favorites above to see your saved ayahs.");
      ayahDiv.querySelector(".ayah-actions button:nth-child(2)").innerText = "⭐ Remove from Favorites";
    }
  }

  function copyAyah(id) {
    const ayahDiv = document.getElementById("ayah-" + id);
    const surahAyah = ayahDiv.querySelector("strong").innerText;
    const quran = ayahDiv.querySelector(".quran").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;
    const tafsirDiv = ayahDiv.querySelector(".tafsir");
    const tafsir = tafsirDiv ? tafsirDiv.innerText : "";

    const text = surahAyah + "\n" + quran + "\n" + translation + (tafsir ? "\n" + tafsir : "");
    navigator.clipboard.writeText(text);
    alert("Copied to clipboard");
  }

  function shareAyah(id) {
    const ayahDiv = document.getElementById("ayah-" + id);
    const surahAyah = ayahDiv.querySelector("strong").innerText;
    const quran = ayahDiv.querySelector(".quran").innerText;
    const translation = ayahDiv.querySelector(".translation").innerText;

    const text = surahAyah + "\n" + quran + "\n" + translation;

    if (navigator.share) {
      navigator.share({ text })
        .then(() => console.log("Shared successfully"))
        .catch(err => console.error("Error sharing:", err));
    } else {
      // Desktop fallback: show social media links
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

  loadSurahs();
</script>

<script>
function shareAyah(surah, ayah, text) {
    const shareUrl = window.location.origin + "/surah.php?surah=" + surah + "&ayah=" + ayah;

    if (navigator.share) {
        navigator.share({
            title: "Maranao Tafsir",
            text: text,
            url: shareUrl
        }).catch(err => console.log("Share cancelled:", err));
    } else {
        const fbUrl = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);
        window.open(fbUrl, "_blank", "width=600,height=400");
    }
}
</script>
