<?php
// Include database connection
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    <title>Surah Index - Maranaw Tafsir</title>
    <link rel="stylesheet" href="style_landing.css">
    <style>
        /* Surah index styling */
        .surah-index {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .surah-index h1 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .surah-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .surah-table td {
            vertical-align: top;
            padding: 15px;
            border: 1px solid #ccc;
            transition: background-color 0.3s, transform 0.2s;
        }

        /* Zebra stripe effect - more noticeable */
        .surah-table tr:nth-child(odd) td {
            background-color: #dcdcdc; /* darker gray */
        }

        .surah-table tr:nth-child(even) td {
            background-color: #f0f0f0; /* lighter gray */
        }

        /* Hover effect on cells */
        .surah-table td:hover {
            background-color: #e0e0e0;
            transform: scale(1.03);
        }

        /* Surah link styling */
        .surah-link {
            text-decoration: none;
            color: #000;
            font-size: 18px;
        }

        .surah-link:hover {
            color: #333;
        }

        /* Arabic font */
        .arabic {
            font-family: 'Amiri', serif;
            font-size: 22px;
            margin-left: 5px;
        }
    </style>
<link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body>
<?php include 'header.php'; ?>
<hr>

<main class="site-content">
    <div class="surah-index">
        <h1>Surah Index</h1>
        <table class="surah-table">
            <tbody>
                <?php
                // Fetch all Surahs
                $stmt = $conn->prepare("SELECT surah_number, surah_name_en, surah_name_ar FROM surahs ORDER BY surah_number ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                $count = 0;
                echo "<tr>";
                while($row = $result->fetch_assoc()) {
                    $surah_number = $row['surah_number'];
                    $english_name = htmlspecialchars($row['surah_name_en']);
                    $arabic_name = htmlspecialchars($row['surah_name_ar']);

                    echo "<td>";
                    echo "<a class='surah-link' href='surah.php?surah={$surah_number}'>";
                    echo "<strong>{$surah_number}. {$english_name} | <span class='arabic'>{$arabic_name}</span></strong>";
                    echo "</a>";
                    echo "</td>";

                    $count++;
                    if ($count % 3 == 0) {
                        echo "</tr><tr>";
                    }
                }
                echo "</tr>";

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
<hr>
    <footer>
        <div class="site-footer">
            <p>&copy; <?php echo date("Y"); ?> Maranaw Tafsir by <strong>Abu Ahmad Tamano.</strong> | Site developed by <strong>Ashary Tamano</strong>. | All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
