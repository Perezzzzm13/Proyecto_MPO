<?php

function exigirAdmin(): void
{
    session_start();

    // Todos los endpoints de administracion pasan por esta comprobacion.
    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Debes iniciar sesion.'
        ]);
        exit;
    }

    if (($_SESSION['rol'] ?? '') !== 'admin') {
        echo json_encode([
            'success' => false,
            'message' => 'No tienes permisos para gestionar usuarios.'
        ]);
        exit;
    }
}

function obtenerJson(): array
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Si el cuerpo no trae JSON valido, se devuelve un array vacio para validar despues.
    return is_array($data) ? $data : [];
}

function contarAdmins(PDO $conexion): int
{
    // Se usa para impedir que el sistema se quede sin ningun administrador.
    $stmt = $conexion->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
    return (int) $stmt->fetchColumn();
}
