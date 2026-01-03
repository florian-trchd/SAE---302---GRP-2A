<?php
require_once("../config.php");

$ip = $_GET["ip"] ?? "";
$ip = trim($ip);

if ($ip === "") {
    echo json_encode([]);
    exit;
}

// On récupère toutes les findings pour cette IP
$sql = "
    SELECT
        f.id,
        f.target       AS ip,
        NULL           AS port,        -- à remplir plus tard si tu stockes les ports
        f.title        AS type,
        f.severity,
        f.description
    FROM findings f
    WHERE f.target = :ip
    ORDER BY f.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":ip" => $ip]);
$rows = $stmt->fetchAll();

$result = [];
foreach ($rows as $r) {
    $result[] = [
        "id"          => (int)$r["id"],
        "ip"          => $r["ip"],
        "port"        => $r["port"], // NULL pour le moment
        "type"        => $r["type"],
        "risque"      => severityToRisque($r["severity"]),
        "description" => $r["description"],
    ];
}

echo json_encode($result);

