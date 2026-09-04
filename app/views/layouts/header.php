<header>
    <h1>Creador de hábitos</h1>

    <nav class="menu-navegacion">
        <ul>
            <li><a href="/creador_habitos/public/index.php?accion=hoy">Hoy</a></li>
            <li><a href="/creador_habitos/public/index.php?accion=estadisticas">Estadísticas</a></li>
            <li><a href="/creador_habitos/public/index.php?accion=crearTarea">Crear Tarea</a></li>
        </ul>
    </nav>
</header>

<div id="modal-logout" class="modal hidden">
    <div class="modal-content">
        <button onclick="window.location.href='/creador_habitos/public/index.php?accion=logout'">
            Cerrar sesión
        </button>
    </div>
</div>

<div id="modal-cambiarContrasenna" class="modal hidden">
    <div class="modal-content">
        <button onclick="window.location.href='/creador_habitos/public/index.php?accion=cambiarContrasenna'">
            Cambiar contraseña
        </button>
    </div>
</div>

<script src="/creador_habitos/public/js/script.js"></script>