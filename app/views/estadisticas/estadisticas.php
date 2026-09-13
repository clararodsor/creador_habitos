<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estadísticas</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
    <link rel="stylesheet" href="/creador_habitos/public/css/bootstrap.min.css">
</head>

<body>

    <?php include __DIR__ . "/../layouts/header.php"; ?>

    <h2>Estadísticas</h2>

    <?php foreach ($tareas as $tarea): ?>

        <div class="tarea">

            <div class="nombre-tarea">
                <?= htmlspecialchars($tarea["nombre"]) ?>
            </div>

            <p>
                Racha actual: <?= $tarea["racha"] ?>
            </p>

            <p>
                Total puntos obtenidos:
                <?= $tarea["puntosAcumulados"] ?>
            </p>

            <p>
                <?= $tarea["esHabito"] ? "Hábito" : "Propósito" ?>
            </p>

            <details>

                <summary>Ver más estadísticas</summary>

                <div class="estadisticas-adicionales">

                    <p>
                        Cumplida <?= $tarea["contCumplido"] ?> veces.
                    </p>

                    <p>
                        Pospuesta <?= $tarea["contPospuesto"] ?> veces.
                    </p>

                    <p>
                        Fallada <?= $tarea["contFallado"] ?> veces.
                    </p>

                    <p>
                        Porcentaje cumplimiento
                        <?= $tarea["porcentaje"] ?>%
                    </p>

                    <p>
                        Mayor racha histórica:
                        <?= $tarea["maxRacha"] ?>
                    </p>

                </div>

            </details>

        </div>

    <?php endforeach; ?>

    <h3>
        Puntos de todas las tareas: <?= $totalPuntos ?>
    </h3>

    <script src="/creador_habitos/public/js/script.js"></script>

</body>

</html>