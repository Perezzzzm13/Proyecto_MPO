<?php
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $sql = "SELECT 
                s.id_sesion,
                s.fecha_hora,
                s.observaciones,
                r.nombre AS nombre_rutina
            FROM sesiones s
            INNER JOIN rutinas r 
                ON s.id_rutina = r.id_rutina
            WHERE s.id_usuario = :id_usuario
            ORDER BY s.fecha_hora DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    $sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'sesiones' => $sesiones
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener las sesiones'
    ]);
}