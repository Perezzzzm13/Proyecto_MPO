<?php

$host = "localhost";
$dbname = "gymtracker";
$username = "root";
$password = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión correcta";

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
