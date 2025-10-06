<?php
include '../config.php'; // adjust path if needed

header("Content-Type: application/json; charset=UTF-8");

// Get query and exact flag
$q = isset($_GET['q']) ? trim($_GET['q']) : "";
$exact = isset($_GET['exact']) ? intval($_GET['exact']) : 0;

if ($q === "") {
    echo json_encode([]);
    exit;
}

// Split keywords
$keywords = preg_split('/\s+/', $q);
$conditions = [];
$params = [];
$types = "";

// Build WHERE
foreach ($keywords as $word) {
    if ($exact === 1) {
        // Exact match using REGEXP word boundaries
        $conditions[] = "(quran_text REGEXP '[[:<:]]{$word}[[:>:]]' 
                          OR maranao_translation REGEXP '[[:<:]]{$word}[[:>:]]' 
                          OR tafsir_text_original REGEXP '[[:<:]]{$word}[[:>:]]')";
    } else {
        // Substring match using LIKE
        $like = "%" . $word . "%";
        $conditions[] = "(quran_text LIKE ? OR maranao_translation LIKE ? OR tafsir_text_original LIKE ?)";
        $params[] = $like; 
        $params[] = $like;
        $params[] = $like;
        $types .= "sss";
    }
}

$where = implode(" OR ", $conditions);
$sql = "SELECT surah, ayah, quran_text, maranao_translation, tafsir_text_original 
        FROM quran_tafsir 
        WHERE $where 
        ORDER BY surah, ayah ASC";

$stmt = $conn->prepare($sql);

// Bind params only if LIKE was used
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
