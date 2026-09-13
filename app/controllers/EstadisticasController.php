<?php
/**
 * Controlador que gestiona las estadísticas de las tareas y las calcula.
 */

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

    /**
     * Calcula y muestra las estadísticas de las tareas del usuario.
     * 
     * Muestra el total de puntos obtenidos y las tareas del usuario. Para cada tarea muestra
     * su nombre, racha actual, puntos obtenidos, estado (propósito/hábito), número de veces cumplida,
     * fallada y pospuesta, porcentaje de cumplimiento de las 10 últimas veces (sin contar las pospuestas)
     * y mayor racha histórica.
     */
    public function mostrar()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }

        $idUsuario = $_SESSION["idUsuario"];
        $tareas = $this->tareaModel->obtenerPorUsuario($idUsuario);
        $idsTareas = [];
        foreach ($tareas as $tarea) {
            $idsTareas[] = $tarea["idTarea"];
        }

        $registrosPorTarea = $this->registroModel->obtenerRegistrosPorTareas($idsTareas);

        $totalPuntos = $this->tareaModel->obtenerSumaPuntos($idUsuario);

        foreach ($tareas as $index => $tarea) {

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

                if ($registro["estado"] == 'C') {
                    $contCumplido++;
                    $veces++;
                    $contRacha++;
                    if ($contRacha > $maxRacha) {
                        $maxRacha = $contRacha;
                    }

                } elseif ($registro["estado"] == 'P') {
                    $contPospuesto++;

                } else {
                    $contFallado++;
                    $veces++;
                    $contRacha = 0;
                }

                if ($veces <= 10) {
                    $total = $contCumplido + $contFallado;
                    $porcentaje = ceil($total > 0 ? ($contCumplido / $total) * 100 : 0);
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