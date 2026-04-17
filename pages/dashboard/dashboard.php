<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../auth/login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/pages/dashboard.css">
</head>
<body>
<h1>Bienvenido a GymTracker</h1>

<p>Hola, <?php echo $_SESSION['nombre']; ?></p>

<div>
    <a href="../routines/routines.html">Mis rutinas</a>
    <a href="../sessions/sessions.html">Mis sesiones</a>
</div>

<a href="../../backend/auth/logout.php">Cerrar sesión</a>
</body>
</html>