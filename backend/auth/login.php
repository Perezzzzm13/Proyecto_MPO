<?php 
session_start();

require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($email === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Debe introducir el email y la contraseña'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe introducir un email válido'
    ]);
    exit;
}

try {
    $sql = 'SELECT id_usuario, nombre_usuario, nombre, email, password, rol
            FROM usuarios
            WHERE email = :email';

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':email' => $email
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$usuario) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario no existe.'
        ]);
        exit;
    }

    if (!password_verify($password, $usuario['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'La contraseña es incorrecta'
        ]);
        exit;
    }

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['rol'] = $usuario['rol'];

    echo json_encode([
        'success' => true,
        'message' => 'Login correcto.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error durante el login'
    ]);
}

?>