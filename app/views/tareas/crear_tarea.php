<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Tarea</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
</head>

<body>

    <?php include __DIR__ . "/../layouts/header.php"; ?>

    <?php if (isset($mensaje)): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <h2>Crear tarea</h2>

    <form id="formulario" method="POST">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre">

        <br><br>

        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" placeholder="Descripción">

        <br><br>

        <p>¿Qué días se tiene que cumplir?</p>

        <label for="mon">Lunes:</label>
        <input type="checkbox" name="mon" value="1">

        <br><br>

        <label for="tue">Martes:</label>
        <input type="checkbox" name="tue" value="1">

        <br><br>

        <label for="wed">Miércoles:</label>
        <input type="checkbox" name="wed" value="1">

        <br><br>

        <label for="thu">Jueves:</label>
        <input type="checkbox" name="thu" value="1">

        <br><br>

        <label for="fri">Viernes:</label>
        <input type="checkbox" name="fri" value="1">

        <br><br>

        <label for="sat">Sábado:</label>
        <input type="checkbox" name="sat" value="1">

        <br><br>

        <label for="sun">Domingo:</label>
        <input type="checkbox" name="sun" value="1">

        <br><br>

        <button type="submit">Guardar</button>

    </form>

    <script src="/creador_habitos/public/js/script.js"></script>

    <div id="modal-error" class="modal hidden">
        <div class="modal-content">
            <p id="modal-text"></p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <script>
            mostrarModal(<?= json_encode($error) ?>);
        </script>
    <?php endif; ?>

</body>

</html>