<?php

require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../models/Registro.php";

class EstadisticasController
{
    private $tareaModel;
    private $registroModel;

    public function __construct($conn)
    {
        $this->tareaModel = new Tarea($conn);
        $this->registroModel = new Registro($conn);
    }

    public function mostrar()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /index.php?accion=login");
            exit;
        }

        $idUsuario = $_SESSION["idUsuario"];

        $tareas = $this->tareaModel->obtenerPorUsuario($idUsuario);

        $idsTareas = [];

        foreach ($tareas as $tarea) {
            $idsTareas[] = $tarea["idTarea"];
        }

        $registrosPorTarea = $this->registroModel->obtenerPorTareas($idsTareas);

        $totalPuntos = 0;

        foreach ($tareas as $index => $tarea) {

            $totalPuntos += $tarea["puntosAcumulados"];

            $id = $tarea["idTarea"];

            $registros = $registrosPorTarea[$id] ?? [];

            $contCumplido = 0;
            $contPospuesto = 0;
            $contFallado = 0;

            $veces = 0;
            $contRacha = 0;
            $maxRacha = 0;
            $porcentaje = 0;

            foreach ($registros as $registro) {

                if ($registro["cumplido"] == 1) {

                    $contCumplido++;
                    $veces++;
                    $contRacha++;

                    if ($contRacha > $maxRacha) {
                        $maxRacha = $contRacha;
                    }

                } elseif ($registro["pospuesto"] == 1) {

                    $contPospuesto++;

                } else {

                    $contFallado++;
                    $veces++;
                    $contRacha = 0;
                }

                if ($veces <= 10) {

                    $total = $contCumplido + $contFallado;

                    $porcentaje = ceil(
                        $total > 0
                        ? ($contCumplido / $total) * 100
                        : 0
                    );
                }
            }

            $tareas[$index]["contCumplido"] = $contCumplido;
            $tareas[$index]["contPospuesto"] = $contPospuesto;
            $tareas[$index]["contFallado"] = $contFallado;
            $tareas[$index]["porcentaje"] = $porcentaje;
            $tareas[$index]["maxRacha"] = $maxRacha;
        }

        require __DIR__ . "/../views/estadisticas/estadisticas.php";
    }
}