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
$id_sesion = $_GET['id'] ?? '';

if ($id_sesion === '' || !is_numeric($id_sesion)) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

try {
    $sqlSesion = "SELECT 
                    s.id_sesion,
                    s.fecha_hora,
                    s.observaciones,
                    r.nombre AS nombre_rutina
                  FROM sesiones s
                  INNER JOIN rutinas r 
                    ON s.id_rutina = r.id_rutina
                  WHERE s.id_sesion = :id_sesion
                    AND s.id_usuario = :id_usuario";

    $stmtSesion = $conexion->prepare($sqlSesion);
    $stmtSesion->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
    $stmtSesion->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtSesion->execute();

    $sesion = $stmtSesion->fetch(PDO::FETCH_ASSOC);

    if (!$sesion) {
        echo json_encode([
            'success' => false,
            'message' => 'La sesión no existe o no pertenece al usuario'
        ]);
        exit;
    }

    $sqlEjercicios = "SELECT 
                        e.nombre,
                        se.numero_serie,
                        se.repeticiones,
                        se.peso
                      FROM sesion_ejercicios se
                      INNER JOIN ejercicios e
                        ON se.id_ejercicio = e.id_ejercicio
                      WHERE se.id_sesion = :id_sesion
                      ORDER BY e.nombre ASC, se.numero_serie ASC";

    $stmtEjercicios = $conexion->prepare($sqlEjercicios);
    $stmtEjercicios->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
    $stmtEjercicios->execute();

    $ejercicios = $stmtEjercicios->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'sesion' => $sesion,
        'ejercicios' => $ejercicios
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener la sesión'
    ]);
}