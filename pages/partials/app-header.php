<div class="contenedor-app">
    <header class="barra-superior">
        <div class="interior-barra-superior">
            <a href="../dashboard/dashboard.php" class="marca-app">
                <span class="icono-marca">
                    <img src="../../assets/img/Logo1.png" alt="GymTracker">
                </span>
                <span class="texto-marca">
                    <strong>GymTracker</strong>
                    <span>Rutinas y progreso</span>
                </span>
            </a>

            <nav class="navegacion-app" aria-label="Navegacion principal">
                <a href="../dashboard/dashboard.php"<?php echo ($paginaActiva ?? '') === 'panel' ? ' class="activo"' : ''; ?>>Inicio</a>
                <a href="../routines/routines.php"<?php echo ($paginaActiva ?? '') === 'rutinas' ? ' class="activo"' : ''; ?>>Rutinas</a>
                <a href="../sessions/sessions.php"<?php echo ($paginaActiva ?? '') === 'sesiones' ? ' class="activo"' : ''; ?>>Sesiones</a>
                <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                    <a href="../admin/users.php"<?php echo ($paginaActiva ?? '') === 'usuarios' ? ' class="activo"' : ''; ?>>Usuarios</a>
                <?php endif; ?>
                <a href="../../backend/auth/logout.php" class="navegacion-peligro">Salir</a>
            </nav>
        </div>
    </header>
