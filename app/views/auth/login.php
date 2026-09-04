<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
</head>

<body class="login">

    <h2>Iniciar sesión</h2>

    <form method="POST">

        <p>Nombre de usuario</p>
        <input type="text" name="nombre" placeholder="Usuario">

        <p>Contraseña</p>
        <input type="password" name="contrasenna" placeholder="Contraseña">

        <br><br>

        <button type="submit">Entrar</button>

        <br><br>

        <a href="/creador_habitos/public/index.php?accion=crearCuenta">Crear cuenta</a>

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