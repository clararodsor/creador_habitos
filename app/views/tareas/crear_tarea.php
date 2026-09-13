<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Tarea</title>
    <link rel="stylesheet" href="/creador_habitos/public/css/estilos.css">
    <link rel="stylesheet" href="/creador_habitos/public/css/bootstrap.min.css">
</head>

<body>

    <?php include __DIR__ . "/../layouts/header.php"; ?>

    <?php if (isset($mensaje)): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <h2>Crear tarea</h2>

    <form id="formularioCrearTarea" method="POST">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre">

        <br><br>

        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" placeholder="Descripción">

        <br><br>

        <p>¿Cuando no se cumpla la tarea, si no está programada para el día siguiente, se puede posponer?</p>
        <label for="esPosponible">Es posponible: </label>
        <input type="checkbox" id="esPosponible" name="esPosponible" value="1">

        <br></br>

        <p>¿Se repite semanalmente o mensualmente?</p>

        <input type="radio" id="semanal" name="esSemanal" value="1">
        <label for="semanal">Semanalmente</label><br>

        <input type="radio" id="mensual" name="esSemanal" value="0">
        <label for="mensual">Mensualmente</label><br>


        <div id="diasSemana">
            <br></br>
            <p>¿Qué día(s) de la semana se tiene que cumplir?</p>

            <label for="mon">Lunes:</label>
            <input type="checkbox" id="mon" name="mon" value="1">

            <br>

            <label for="tue">Martes:</label>
            <input type="checkbox" id="tue" name="tue" value="1">

            <br>

            <label for="wed">Miércoles:</label>
            <input type="checkbox" id="wed" name="wed" value="1">

            <br>

            <label for="thu">Jueves:</label>
            <input type="checkbox" id="thu" name="thu" value="1">

            <br>

            <label for="fri">Viernes:</label>
            <input type="checkbox" id="fri" name="fri" value="1">

            <br>

            <label for="sat">Sábado:</label>
            <input type="checkbox" id="sat" name="sat" value="1">

            <br>

            <label for="sun">Domingo:</label>
            <input type="checkbox" id="sun" name="sun" value="1">

        </div>

        <div id="diasMes">
            <br></br>
            <p>¿Qué día(s) del mes se tiene que cumplir?</p>

            <div>
                <label for="dia1">Día 1:</label>
                <input type="checkbox" id="dia1" name="dia1" value="1">
            </div>

            <div>
                <label for="dia2">Día 2:</label>
                <input type="checkbox" id="dia2" name="dia2" value="1">
            </div>

            <div>
                <label for="dia3">Día 3:</label>
                <input type="checkbox" id="dia3" name="dia3" value="1">
            </div>

            <div>
                <label for="dia4">Día 4:</label>
                <input type="checkbox" id="dia4" name="dia4" value="1">
            </div>

            <div>
                <label for="dia5">Día 5:</label>
                <input type="checkbox" id="dia5" name="dia5" value="1">
            </div>

            <div>
                <label for="dia6">Día 6:</label>
                <input type="checkbox" id="dia6" name="dia6" value="1">
            </div>

            <div>
                <label for="dia7">Día 7:</label>
                <input type="checkbox" id="dia7" name="dia7" value="1">
            </div>

            <div>
                <label for="dia8">Día 8:</label>
                <input type="checkbox" id="dia8" name="dia8" value="1">
            </div>

            <div>
                <label for="dia9">Día 9:</label>
                <input type="checkbox" id="dia9" name="dia9" value="1">
            </div>

            <div>
                <label for="dia10">Día 10:</label>
                <input type="checkbox" id="dia10" name="dia10" value="1">
            </div>

            <div>
                <label for="dia11">Día 11:</label>
                <input type="checkbox" id="dia11" name="dia11" value="1">
            </div>

            <div>
                <label for="dia12">Día 12:</label>
                <input type="checkbox" id="dia12" name="dia12" value="1">
            </div>

            <div>
                <label for="dia13">Día 13:</label>
                <input type="checkbox" id="dia13" name="dia13" value="1">
            </div>

            <div>
                <label for="dia14">Día 14:</label>
                <input type="checkbox" id="dia14" name="dia14" value="1">
            </div>

            <div>
                <label for="dia15">Día 15:</label>
                <input type="checkbox" id="dia15" name="dia15" value="1">
            </div>

            <div>
                <label for="dia16">Día 16:</label>
                <input type="checkbox" id="dia16" name="dia16" value="1">
            </div>

            <div>
                <label for="dia17">Día 17:</label>
                <input type="checkbox" id="dia17" name="dia17" value="1">
            </div>

            <div>
                <label for="dia18">Día 18:</label>
                <input type="checkbox" id="dia18" name="dia18" value="1">
            </div>

            <div>
                <label for="dia19">Día 19:</label>
                <input type="checkbox" id="dia19" name="dia19" value="1">
            </div>

            <div>
                <label for="dia20">Día 20:</label>
                <input type="checkbox" id="dia20" name="dia20" value="1">
            </div>

            <div>
                <label for="dia21">Día 21:</label>
                <input type="checkbox" id="dia21" name="dia21" value="1">
            </div>

            <div>
                <label for="dia22">Día 22:</label>
                <input type="checkbox" id="dia22" name="dia22" value="1">
            </div>

            <div>
                <label for="dia23">Día 23:</label>
                <input type="checkbox" id="dia23" name="dia23" value="1">
            </div>

            <div>
                <label for="dia24">Día 24:</label>
                <input type="checkbox" id="dia24" name="dia24" value="1">
            </div>

            <div>
                <label for="dia25">Día 25:</label>
                <input type="checkbox" id="dia25" name="dia25" value="1">
            </div>

            <div>
                <label for="dia26">Día 26:</label>
                <input type="checkbox" id="dia26" name="dia26" value="1">
            </div>

            <div>
                <label for="dia27">Día 27:</label>
                <input type="checkbox" id="dia27" name="dia27" value="1">
            </div>

            <div>
                <label for="dia28">Día 28:</label>
                <input type="checkbox" id="dia28" name="dia28" value="1">
            </div>

            <div>
                <label for="dia29">Día 29:</label>
                <input type="checkbox" id="dia29" name="dia29" value="1">
            </div>

            <div>
                <label for="dia30">Día 30:</label>
                <input type="checkbox" id="dia30" name="dia30" value="1">
            </div>

            <div>
                <label for="dia31">Día 31:</label>
                <input type="checkbox" id="dia31" name="dia31" value="1">
            </div>

        </div>

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