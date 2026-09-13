<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Hoy</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
    <link rel="stylesheet" href="/creador_habitos/public/css/bootstrap.min.css">
</head>

<body>

    <?php include __DIR__ . "/../layouts/header.php"; ?>

    <h2>Tareas de hoy</h2>

    <form method="POST">

        <?php if ($tareas): ?>
            <?php foreach ($tareas as $tarea): ?>

                <div class="tarea">
                    <input type="checkbox" id="tarea-<?= $tarea["idTarea"] ?>" name="tareas[]" value="<?= $tarea["idTarea"] ?>">
                    <label for="tarea-<?= $tarea["idTarea"] ?>">
                        <div class="nombre-tarea">
                            <?= htmlspecialchars($tarea["nombre"]) ?>
                        </div>
                    </label>

                    <?php if (!empty($tarea["descripcion"])): ?>
                        <p>
                            Descripción:
                            <?= htmlspecialchars($tarea["descripcion"]) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($tarea["esHabito"]): ?>
                        <p>Puntos: 5</p>
                    <?php else: ?>
                        <p>
                            Puntos:
                            <?= htmlspecialchars($tarea["racha"] + 1) ?>
                        </p>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p>No hay tareas para hoy.</p>
        <?php endif; ?>

    </form>

    <script src="/creador_habitos/public/js/script.js"></script>

</body>

</html>