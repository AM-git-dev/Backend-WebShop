<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require '../config/database.php';
require '../models/User.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    echo json_encode(["error" => "Email et mot de passe requis"]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

$user = new User($pdo);
$userData = $user->findByEmail($email);

if (!$userData || !password_verify($password, $userData['password'])) {
    echo json_encode(["error" => "Identifiants incorrects"]);
    exit;
}

$payload = [
    "id" => $userData['id'],
    "email" => $userData['email'],
    "role" => $userData['role'],
    "exp" => time() + 3600
];
$secret = $_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? 'fallback_secret';

$jwt = JWT::encode($payload, $secret, 'HS256');
echo json_encode([
    "token" => $jwt,
    "user" => [
        "id" => $userData['id'],
        "email" => $userData['email'],
        "role" => $userData['role']
    ]
]);
exit;



