<?php
require_once '../config/database.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');

exigirAdmin();

try {
    $sql = 'SELECT id_usuario, nombre_usuario, nombre, apellidos, email, rol, fecha_registro
            FROM usuarios
            ORDER BY fecha_registro DESC, id_usuario DESC';

    $stmt = $conexion->query($sql);

    echo json_encode([
        'success' => true,
        'current_user_id' => (int) $_SESSION['id_usuario'],
        'usuarios' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error al cargar los usuarios.'
    ]);
}
