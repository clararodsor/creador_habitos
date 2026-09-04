<?php

require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../models/Registro.php";

class TareaController
{
    private $conn;
    private $tareaModel;
    private $registroModel;

    private const PUNTOS_HABITO = 5;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->tareaModel = new Tarea($conn);
        $this->registroModel = new Registro($conn);
    }

    public function crear()
    {
        $error = null;

        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $idUsuario = $_SESSION["idUsuario"];

            $nombre = $_POST["nombre"] ?? "";
            $descripcion = $_POST["descripcion"] ?? "";

            $mon = isset($_POST["mon"]) ? 1 : 0;
            $tue = isset($_POST["tue"]) ? 1 : 0;
            $wed = isset($_POST["wed"]) ? 1 : 0;
            $thu = isset($_POST["thu"]) ? 1 : 0;
            $fri = isset($_POST["fri"]) ? 1 : 0;
            $sat = isset($_POST["sat"]) ? 1 : 0;
            $sun = isset($_POST["sun"]) ? 1 : 0;

            if (empty($nombre)) {
                $error = "El nombre es obligatorio";

            } elseif (
                $mon === 0 &&
                $tue === 0 &&
                $wed === 0 &&
                $thu === 0 &&
                $fri === 0 &&
                $sat === 0 &&
                $sun === 0
            ) {
                $error = "Debes seleccionar al menos un día.";

            } else {

                $resultado = $this->tareaModel->crear(
                    $idUsuario,
                    $nombre,
                    $descripcion,
                    $mon,
                    $tue,
                    $wed,
                    $thu,
                    $fri,
                    $sat,
                    $sun
                );

                if ($resultado) {
                    $error = "Tarea creada correctamente";
                } else {
                    $error = "Error al guardar la tarea";
                }
            }
        }

        require __DIR__ . "/../views/tareas/crear_tarea.php";
    }

    public function hoy()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }

        $idUsuario = $_SESSION["idUsuario"];

        $hoyDate = date("Y-m-d");
        $diaSemanaHoy = strtolower(date("D"));

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $tareasMarcadas = $_POST["tareas"] ?? [];

            $resultado = $this->guardarDia(
                $idUsuario,
                $diaSemanaHoy,
                $tareasMarcadas
            );

            if (!$resultado["exito"]) {
                die("Error al guardar el día: " . $resultado["mensaje"]);
            }

            header("Location: /creador_habitos/public/index.php?accion=hoy");
            exit;
        }

        $tareas = $this->tareaModel->obtenerParaHoy(
            $idUsuario,
            $diaSemanaHoy
        );

        $yaGuardado = $this->registroModel->existeRegistroHoy(
            $idUsuario,
            $hoyDate
        );

        require __DIR__ . "/../views/tareas/hoy.php";
    }

    private function guardarDia(
        $idUsuario,
        $diaSemanaHoy,
        $tareasMarcadas
    ) {
        $hoy = date("Y-m-d");

        $diaSemanaManana = strtolower(
            date("D", strtotime("+1 day"))
        );

        $esLunesManana = (
            date("N", strtotime("+1 day")) == 1
        );

        $conn = $this->conn;

        $tareas = $this->tareaModel->obtenerParaGuardar(
            $idUsuario,
            $diaSemanaHoy
        );

        $conn->begin_transaction();

        try {

            foreach ($tareas as $tarea) {

                $idTarea = $tarea["idTarea"];

                $cumplido = in_array(
                    $idTarea,
                    $tareasMarcadas
                ) ? 1 : 0;

                $puntos = 0;
                $pospuesto = 0;

                $mananaToca = !empty(
                    $tarea[$diaSemanaManana]
                );

                if ($cumplido) {

                    if ($tarea["esHabito"]) {

                        $puntos = self::PUNTOS_HABITO;

                    } else {

                        $puntos = $tarea["racha"] + 1;
                    }

                    $resultado = $this->tareaModel->actualizarCumplida(
                        $idTarea,
                        $puntos
                    );

                    if (!$resultado) {
                        throw new Exception(
                            "No se pudo actualizar la tarea."
                        );
                    }

                } elseif (!$mananaToca && !$esLunesManana) {

                    $pospuesto = 1;

                } else {

                    $resultado = $this->tareaModel->reiniciarRacha(
                        $idTarea
                    );

                    if (!$resultado) {
                        throw new Exception(
                            "No se pudo reiniciar la racha."
                        );
                    }
                }

                $historial = $this->registroModel->obtenerHistorial(
                    $idTarea
                );

                $contCumplido = 0;
                $contador = 0;

                foreach ($historial as $registro) {

                    if ($registro["pospuesto"] == 1) {
                        continue;
                    }

                    $contador++;

                    if ($registro["cumplido"] == 1) {
                        $contCumplido++;
                    }

                    if ($contador == 10) {
                        break;
                    }
                }

                $nuevosAcumulados =
                    $tarea["puntosAcumulados"];

                if (
                    $cumplido &&
                    !$tarea["esHabito"]
                ) {
                    $nuevosAcumulados += $puntos;
                }

                if (
                    !$tarea["esHabito"] &&
                    $nuevosAcumulados >= 200 &&
                    $contCumplido >= 8
                ) {

                    $resultado =
                        $this->tareaModel->convertirEnHabito(
                            $idTarea
                        );

                    if (!$resultado) {
                        throw new Exception(
                            "No se pudo convertir la tarea en hábito."
                        );
                    }
                }

                $resultado =
                    $this->registroModel->crear(
                        $idTarea,
                        $cumplido,
                        $pospuesto,
                        $puntos
                    );

                if (!$resultado) {
                    throw new Exception(
                        "No se pudo crear el registro."
                    );
                }
            }

            $conn->commit();

            return [
                "exito" => true,
                "mensaje" => ""
            ];

        } catch (Exception $e) {

            $conn->rollback();

            return [
                "exito" => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }

}