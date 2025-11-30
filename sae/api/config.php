<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); // ton site doit pouvoir accéder
header("Access-Control-Allow-Methods: GET");


try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sae302;charset=utf8",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion BDD"]);
    exit;
}
