<?php

$host = "sql102.infinityfree.com";
$dbname = "if0_42003014_Gymtracker";
$username = "if0_42003014";
$password = "45899818Y";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
