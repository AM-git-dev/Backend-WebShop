<?php
require '../config/database.php';
require '../models/User.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


header('Content-type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'], $data['email'], $data['password'])) {
    echo json_encode(["error" => "Tous les champs sont requis"]);
    exit;
}

$username = $data['username'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = 'user';

$user = new User($pdo);
if($user->findByEmail($email)){
    echo json_encode(["error" => "Cet email est déja utilisé"]);
    exit;
}

$user->createUser($username, $email, $password, $role);
echo json_encode(["message" => "Utilisateur créé avec succès"]);