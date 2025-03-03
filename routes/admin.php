<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../config/database.php';
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 🔍 Récupération du token
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

if (!$authHeader) {
    echo json_encode(["error" => "Accès refusé, aucun token fourni"]);
    http_response_code(403);
    exit;
}

$token = str_replace("Bearer ", "", $authHeader);

// 🔍 Décodage du token
try {
    $secret = $_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? 'fallback_secret';
    $decoded = JWT::decode($token, new Key($secret, 'HS256'));

    // Vérifier que l'utilisateur est un admin
    if ($decoded->role !== 'admin') {
        echo json_encode(["error" => "Accès interdit, vous n'êtes pas administrateur"]);
        http_response_code(403);
        exit;
    }

    // 🔍 Récupération des produits
    $query = $pdo->query("SELECT * FROM products");
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["products" => $products]);
} catch (Exception $e) {
    echo json_encode([
        "error" => "Token invalide ou expiré",
        "message" => $e->getMessage()
    ]);
    http_response_code(401);
}
