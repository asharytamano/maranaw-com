<div class="sidebar">

    <!-- Search Box -->
    <div class="search-box">
        <h2>Search</h2>
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Enter Surah #, Ayah #, Surah:Ayah, or keywords...">
            <select name="field">
                <option value="all">All Fields</option>
                <option value="arabic">Arabic Text</option>
                <option value="maranao">Maranao Translation</option>
                <option value="tafsir">Tafsir</option>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Search Tips -->
    <div class="search-tips">
        <details>
            <summary>SEARCH TIPS</summary>
            <ul>
                <li>Use <b>2:256</b> (with colon) for Surah 2, Ayah 256</li>
                <li>Or <b>2 256</b> (with space)</li>
                <li>Type keywords in English (e.g., <b>Allah</b>)</li>
            </ul>
        </details>
    </div>

    <!-- The Tafsir Author -->
    <div class="about-me-box">
        <h2>The Tafsir Author</h2>
        <p>
            Engr. Abdulbasit Tamano is a dedicated scholar and author of the Maranao Tafsir. 
            He has worked extensively to make Qur’anic teachings accessible in the Maranao language.
        </p>
        <a href="about.php" class="sidebar-btn">Learn More</a>
    </div>

    <!-- Subscribe Box -->
    <div class="subscribe-box">
        <h2>Subscribe for Updates</h2>
        <form action="subscribe.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
        </form>
        <small>You will be notified when new Tafsir updates are available.</small>
    </div>

    <!-- Daily Verse / Reflection -->
    <div class="sidebar-box">
        <h3>Daily Verse / Reflection</h3>
        <?php
        $mysqli = new mysqli("localhost", "root", "", "mtt_db");
        if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

        $result = $mysqli->query("SELECT surah, ayah, quran_text, maranao_translation FROM quran_tafsir ORDER BY RAND() LIMIT 1");
        if ($result && $row = $result->fetch_assoc()):
        ?>
            <div>
                <strong>Surah <?php echo (int)$row['surah']; ?>, Ayah <?php echo (int)$row['ayah']; ?>:</strong>
                <span><?php echo htmlspecialchars($row['quran_text'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span><?php echo htmlspecialchars($row['maranao_translation'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <a href="surah.php?surah=<?php echo (int)$row['surah']; ?>&ayah=<?php echo (int)$row['ayah']; ?>" class="sidebar-btn">Read full tafsir &raquo;</a>
        <?php else: ?>
            <div>Daily verse not available at the moment.</div>
        <?php endif; ?>
        <?php $mysqli->close(); ?>
    </div>

    <!-- Download Tafsir Box -->
    <div class="download-box">
        <h2>Download Tafsir</h2>
        <a href="https://www.dropbox.com/scl/fi/iflp9ho8e7dmu7jyfk4d9/Maranao-Tafsir_-July2025.pdf?rlkey=uxcr5orzvh1jocvp83axud1ux&st=14xy13w0&dl=0" class="sidebar-btn">Download PDF</a>
        <p>Get the full Tafsir in Maranao and Arabic.</p>
    </div>

    <!-- Contact Us Box -->
    <div class="contact-box">
        <h2>Contact Us</h2>
        <form action="contact.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <div class="sidebar-section">
        <h3>Connect with Us</h3>
        <div class="share-buttons">
            <a href="#" class="sidebar-btn share-btn twitter" id="shareTwitter">Twitter</a>
            <a href="#" class="sidebar-btn share-btn facebook" id="shareFacebook">Facebook</a>
            <a href="#" class="sidebar-btn share-btn whatsapp" id="shareWhatsApp">WhatsApp</a>
        </div>
    </div>
</div>
