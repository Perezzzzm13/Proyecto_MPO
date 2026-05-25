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

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$id_rutina = $data['id_rutina'] ?? '';
$nombre = trim($data['nombre'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');
$id_usuario = $_SESSION['id_usuario'];

// El id se comprueba antes de actualizar para no ejecutar consultas sobre rutinas no validas.
if ($id_rutina === '' || !is_numeric($id_rutina)) {
    echo json_encode([
        'success' => false,
        'message' => 'Id de rutina no valido'
    ]);
    exit;
}

if ($nombre === '') {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre de la rutina es obligatorio'
    ]);
    exit;
}

if (mb_strlen($nombre) > 50 || mb_strlen($descripcion) > 255) {
    echo json_encode([
        'success' => false,
        'message' => 'Los datos de la rutina superan la longitud permitida'
    ]);
    exit;
}

try {
    $sql = "UPDATE rutinas
            SET nombre = :nombre,
                descripcion = :descripcion
            WHERE id_rutina = :id_rutina
              AND id_usuario = :id_usuario";

    // Se filtra por id_usuario para que cada usuario solo pueda modificar sus propias rutinas.
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Rutina actualizada correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar la rutina'
    ]);
}
