<?php
require_once("../config.php");

$ip = $_GET["ip"] ?? "";

$fake = [
    [ "id" => 1, "ip" => "192.168.1.10", "port" => 22, "type" => "SSH ouvert", "risque" => "faible", "description" => "Port SSH accessible" ],
    [ "id" => 2, "ip" => "192.168.1.10", "port" => 23, "type" => "Telnet", "risque" => "élevé", "description" => "Service Telnet non chiffré" ],
    [ "id" => 3, "ip" => "10.0.0.42", "port" => 80, "type" => "HTTP sans TLS", "risque" => "moyen", "description" => "Pas de HTTPS" ]
];

$result = array_filter($fake, fn($v) => $v["ip"] === $ip);

echo json_encode(array_values($result));
