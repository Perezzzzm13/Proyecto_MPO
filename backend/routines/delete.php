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
$id_usuario = $_SESSION['id_usuario'];

if ($id_rutina === '' || !is_numeric($id_rutina)) {
    echo json_encode([
        'success' => false,
        'message' => 'Id de rutina no valido'
    ]);
    exit;
}

try {
    $sql = "DELETE FROM rutinas
            WHERE id_rutina = :id_rutina
              AND id_usuario = :id_usuario";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo eliminar la rutina'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Rutina eliminada correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la rutina'
    ]);
}
