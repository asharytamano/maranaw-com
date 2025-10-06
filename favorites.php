<?php include 'header.php'; ?>
<hr>

<div class="site-content" style="max-width: 1000px; margin: 0 auto;">

  <h1 style="text-align:center;">⭐ Your Favorites</h1>
  <p style="text-align:center; color:#555;">
    Saved Ayahs from the Maranaw Tafsir
  </p>

  <div id="favorites-list"></div>

</div>

<hr>
<?php include 'footer.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Amiri&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

<style>
  .ayah {
    margin-bottom: 20px;
    padding: 12px;
    border-bottom: 1px solid #eee;
  }
  .quran {
    font-family: 'Amiri', serif;
    font-size: 22px;
    line-height: 2;
    color: darkgreen;
    direction: rtl;
    text-align: right;
  }
  .translation {
    font-family: 'Merriweather', serif;
    font-size: 16px;
    line-height: 1.8;
    color: darkblue;
    margin-top: 5px;
    text-align: left;
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
</style>

<script>
  function loadFavorites() {
    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    const listDiv = document.getElementById("favorites-list");

    if (favorites.length === 0) {
      listDiv.innerHTML = "<p style='text-align:center; color:#777;'>No favorites saved yet.</p>";
      return;
    }

    listDiv.innerHTML = "";

    favorites.forEach((f, index) => {
      const div = document.createElement("div");
      div.className = "ayah";
      div.id = "fav-" + index;

      div.innerHTML = `
        <div><strong>${f.surah}</strong></div>
        <div class="quran">${f.quran}</div>
        <div class="translation">${f.translation}</div>

        <div class="ayah-actions">
          <button onclick="removeFavorite(${index})">❌ Remove</button>
          <button onclick="copyFavorite(${index})">📋 Copy</button>
          <button onclick="shareFavorite(${index})">🔗 Share</button>
        </div>
        <div id="share-links-fav-${index}" class="share-links" style="display:none;"></div>
      `;
      listDiv.appendChild(div);
    });
  }

  function removeFavorite(index) {
    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    favorites.splice(index, 1);
    localStorage.setItem("favorites", JSON.stringify(favorites));
    loadFavorites();
  }

  function copyFavorite(index) {
    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    const f = favorites[index];
    const text = f.surah + "\n" + f.quran + "\n" + f.translation;
    navigator.clipboard.writeText(text);
    alert("Copied to clipboard");
  }

  function shareFavorite(index) {
    let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    const f = favorites[index];
    const text = f.surah + "\n" + f.quran + "\n" + f.translation;

    if (navigator.share) {
      navigator.share({ text })
        .then(() => console.log("Shared successfully"))
        .catch(err => console.error("Error sharing:", err));
    } else {
      const shareLinks = document.getElementById("share-links-fav-" + index);
      const encoded = encodeURIComponent(text);
      shareLinks.innerHTML = `
        <a href="https://www.facebook.com/sharer/sharer.php?u=${encoded}" target="_blank">Facebook</a>
        <a href="https://twitter.com/intent/tweet?text=${encoded}" target="_blank">X</a>
        <a href="https://wa.me/?text=${encoded}" target="_blank">WhatsApp</a>
      `;
      shareLinks.style.display = "block";
    }
  }

  loadFavorites();
</script>
