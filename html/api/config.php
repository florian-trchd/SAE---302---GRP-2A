<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sae302;charset=utf8mb4",
        "sae302",
        "sae302pwd"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion BDD"]);
    exit;
}

function severityToRisque(string $sev): string {
    switch (strtoupper($sev)) {
        case 'CRITICAL': return 'critique';
        case 'HIGH':     return 'élevé';
        case 'MEDIUM':   return 'moyen';
        case 'LOW':      return 'faible';
        case 'INFO':     return 'information';
        default:         return strtolower($sev);
    }
}
