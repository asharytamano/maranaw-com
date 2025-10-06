<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Maranao Tafsir</title>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Merriweather', serif;
}

/* Header styles */
header {
    width: 100%;
    background-color: #000;
    color: #fff;
}

.header-inner {
    max-width: 1000px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    flex-wrap: wrap; /* allows wrapping on smaller screens */
}

/* Logo styles */
.logo a {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.logo img {
    width: 64px;  /* default desktop size */
    height: 64px;
    margin-right: 10px;
}

/* Navigation styles */
.main-nav {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}

.main-nav a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    font-family: 'Merriweather', serif;
    font-size: 16px;
}

.main-nav a:hover {
    color: #ccc;
}

/* Horizontal rule */
hr {
    width: 100%;
    border: 1px solid #ccc;
    margin: 0;
}

/* Mobile adjustments */
@media (max-width: 600px) {
    .header-inner {
        flex-direction: column;
        align-items: flex-start;
    }

    .logo img {
        width: 64px;   /* smaller logo on mobile */
        height: 64px;
        margin-right: 8px;
    }

    .main-nav {
        gap: 10px;
        flex-direction: column;
        width: 100%;
    }
}
</style>
</head>
<body>
<header>
<!-- Open Graph (Facebook, Messenger, LinkedIn) -->
<meta property="og:title" content="Maranao Tafsir – Tafsir sa Basa Maranao">
<meta property="og:description" content="Official Maranao Tafsir resource online – rooted in the work of Engr. Abdulbasit Tamano.">
<meta property="og:image" content="https://tafsir.maranaw.com/images/og-image.jpg">
<meta property="og:url" content="https://tafsir.maranaw.com/">
<meta property="og:type" content="website">

<!-- Twitter / X Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Maranao Tafsir – Tafsir sa Basa Maranao">
<meta name="twitter:description" content="Official Maranao Tafsir resource online – rooted in the work of Engr. Abdulbasit Tamano.">
<meta name="twitter:image" content="https://tafsir.maranaw.com/images/og-image.jpg">
<meta name="twitter:site" content="@YourHandle"> <!-- optional -->

<div class="header-inner">
    <div class="logo">
        <a href="index.php">
            <img src="images/logo-64x64.png" alt="Maranao Tafsir Logo">
            Maranao Tafsir
        </a>
    </div>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="home.php">Surah Index</a>
        <a href="contact.php">Contact</a>
        <a href="search.php">Search</a>
        <a href="https://drive.google.com/file/d/18RSfRVKqVJ8DtFxtJoWB9rTqswsYtWFl/view?usp=sharing">Download PDF</a>
    </nav>
</div>

</header>
<hr>
