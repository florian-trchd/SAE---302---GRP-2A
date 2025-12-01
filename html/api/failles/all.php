<?php
require_once("../config.php");

// Toutes les findings, toutes IP confondues
$sql = "
    SELECT
        f.id,
        f.target       AS ip,
        NULL           AS port,
        f.title        AS type,
        f.severity,
        f.description
    FROM findings f
    ORDER BY f.created_at DESC
";

$rows = $pdo->query($sql)->fetchAll();

$result = [];
foreach ($rows as $r) {
    $result[] = [
        "id"          => (int)$r["id"],
        "ip"          => $r["ip"],
        "port"        => $r["port"],
        "type"        => $r["type"],
        "risque"      => severityToRisque($r["severity"]),
        "description" => $r["description"],
    ];
}

echo json_encode($result);
