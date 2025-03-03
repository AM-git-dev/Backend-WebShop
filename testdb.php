<?php
require 'config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT 'Connexion réussie !' AS message");
    $result = $stmt->fetch();
    echo json_encode(["success" => $result['message']]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Échec de la connexion : " . $e->getMessage()]);
}
