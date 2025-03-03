<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Charger les dépendances

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


$secret = $_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? 'FALLBACK_SECRET';


header('Content-Type: application/json');

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Accès refusé, aucun token fourni"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);

try {
    $decoded = JWT::decode($token, new Key($secret, 'HS256'));
    $userId = $decoded->id;
    $userRole = $decoded->role;
} catch (Exception $e) {
    echo json_encode(["error" => "Token invalide"]);
    exit;
}
