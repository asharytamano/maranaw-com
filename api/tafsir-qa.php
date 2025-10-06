<?php
/**
 * tafsir-qa.php
 * Secure OpenAI API handler for Maranaw Tafsir website/app
 * --------------------------------------------------------
 * Loads API key from .env (not from source code) using vlucas/phpdotenv
 */

require_once __DIR__ . '/../vendor/autoload.php'; // Loads Composer dependencies

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Retrieve API key securely
$api_key = $_ENV['OPENAI_API_KEY'] ?? null;

if (!$api_key) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not found. Please set OPENAI_API_KEY in .env']);
    exit;
}

// Read incoming request (from frontend)
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['question']) || empty(trim($input['question']))) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing question parameter']);
    exit;
}

$question = trim($input['question']);

// Prepare API request
$url = "https://api.openai.com/v1/chat/completions";
$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role" => "system", "content" => "You are a knowledgeable Maranaw Islamic scholar specializing in Tafsir and Fiqh."],
        ["role" => "user", "content" => $question]
    ],
    "temperature" => 0.6,
    "max_tokens" => 600
];

$headers = [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
];

// Send request to OpenAI
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

// Return OpenAI response to frontend
echo $response;
