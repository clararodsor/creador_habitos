<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
</head>

<body class="login">

    <h2>Crear cuenta</h2>

    <form method="POST" id="formulario">

        <p>Nombre de usuario</p>
        <input type="text" name="nombre" maxlength="20" placeholder="Nombre de usuario">

        <p>Contraseña</p>
        <input type="password" name="contrasenna" id="contrasenna" placeholder="Contraseña">

        <p>Confirmar contraseña</p>
        <input type="password" name="confirmarContrasenna" id="confirmarContrasenna" placeholder="Repite la contraseña">

        <br></br>

        <button type="submit">Crear usuario</button>

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