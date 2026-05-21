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
                r.nombre AS nombre_rutina,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM sesiones s_anterior
                        WHERE s_anterior.id_usuario = s.id_usuario
                          AND s_anterior.id_rutina = s.id_rutina
                          AND s_anterior.fecha_hora < s.fecha_hora
                    )
                    THEN 1
                    ELSE 0
                END AS tiene_sesion_anterior
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
