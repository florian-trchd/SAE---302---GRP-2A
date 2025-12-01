<?php
require_once("../config.php");

$id = $_GET["id"] ?? "";
$id = (int)$id;

if ($id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT
        f.id,
        f.target       AS ip,
        NULL           AS port,
        f.title        AS type,
        f.severity,
        f.description
    FROM findings f
    WHERE f.id = :id
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode([]);
    exit;
}

$result = [
    "id"          => (int)$row["id"],
    "ip"          => $row["ip"],
    "port"        => $row["port"],
    "type"        => $row["type"],
    "risque"      => severityToRisque($row["severity"]),
    "description" => $row["description"],
];

echo json_encode($result);
