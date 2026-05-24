<?php
session_start();

require_once '../config/database.php';

header('Content-type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$nombreUsuario = trim($data['nombre_usuario'] ?? '');
$nombre = trim($data['nombre'] ?? '');
$apellidos = trim($data['apellidos'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$fechaRegistro = (new DateTime('now', new DateTimeZone('Europe/Madrid')))->format('Y-m-d H:i:s');

if ($nombreUsuario === '' || $nombre === '' || $email === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos por introducir al formulario'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe introducir un email válido.'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 6 caracteres.'
    ]);
    exit;
}

try {
    $sql = 'SELECT id_usuario FROM usuarios WHERE email = :email OR nombre_usuario = :nombreUsuario';
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':nombreUsuario' => $nombreUsuario
    ]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'El correo electrónico o el usuario ya están registrados.'
        ]);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sqlInsert = 'INSERT INTO usuarios (nombre_usuario, nombre, apellidos, email, password, rol, fecha_registro)
              VALUES (:nombre_usuario, :nombre, :apellidos, :email, :password, :rol, :fecha_registro)';

    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->execute([
        ':nombre_usuario' => $nombreUsuario,
        ':nombre' => $nombre,
        ':apellidos' => $apellidos,
        ':email' => $email,
        ':password' => $passwordHash,
        ':rol' => 'usuario',
        ':fecha_registro' => $fechaRegistro
    ]);

    session_regenerate_id(true);

    $_SESSION['id_usuario'] = $conexion->lastInsertId();
    $_SESSION['nombre_usuario'] = $nombreUsuario;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['email'] = $email;
    $_SESSION['rol'] = 'usuario';
    $_SESSION['fecha_registro'] = $fechaRegistro;

    session_write_close();

    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado correctamente.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error durante el registro.'
    ]);
}

