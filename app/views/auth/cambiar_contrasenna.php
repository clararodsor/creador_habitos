<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
    <link rel="stylesheet" href="/creador_habitos/public/css/bootstrap.min.css">
</head>

<body class="login">

    <?php include __DIR__ . "/../layouts/header.php"; ?>

    <h2>Cambiar contraseña</h2>

    <form method="POST" id="formulario">

        <p>Contraseña nueva</p>
        <input type="password" name="nuevaContrasenna" id="nuevaContrasenna" placeholder="Nueva contraseña">

        <p>Confirmar contraseña nueva</p>
        <input type="password" name="confirmarNuevaContrasenna" id="confirmarNuevaContrasenna"
            placeholder="Repite la nueva contraseña">

        <br></br>

        <button type="submit">Cambiar contraseña</button>

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