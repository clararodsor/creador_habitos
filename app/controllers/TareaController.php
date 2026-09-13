<?php
/**
 * Controlador que gestiona las tareas y sus registros diarios.
 */

require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../models/Registro.php";

class TareaController
{
    private $conn;
    private $tareaModel;
    private $registroModel;

    /**
     * @var int Puntos que se obtienen al cumplir una tarea que es hábito.
     */
    private const PUNTOS_HABITO = 5;
    /**
     * @var int Mínimo de veces que se tiene que cumplir un propósito de las 10 últimas para convertirse en un hábito.
     */
    private const MIN_CUMPLIMIENTO_HABITO = 7;
    /**
     * @var int Mínimo de puntos que se deben obtener con un propósito para convertirse en un hábito.
     */
    private const MIN_PUNTOS_HABITO = 200;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->tareaModel = new Tarea($conn);
        $this->registroModel = new Registro($conn);
    }

    /**
     * Crea una tarea, comprobando los datos introducidos y la selección de días.
     */
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
            $esPosponible = !empty($_POST["esPosponible"]) ? 1 : 0;
            $esSemanal = !empty($_POST["esSemanal"]) ? 1 : 0;

            if ($esSemanal == 1) {
                $dias = [
                    'mon' => isset($_POST["mon"]) ? 1 : 0,
                    'tue' => isset($_POST["tue"]) ? 1 : 0,
                    'wed' => isset($_POST["wed"]) ? 1 : 0,
                    'thu' => isset($_POST["thu"]) ? 1 : 0,
                    'fri' => isset($_POST["fri"]) ? 1 : 0,
                    'sat' => isset($_POST["sat"]) ? 1 : 0,
                    'sun' => isset($_POST["sun"]) ? 1 : 0
                ];
            } else {
                $dias = [
                    'dia1' => isset($_POST["dia1"]) ? 1 : 0,
                    'dia2' => isset($_POST["dia2"]) ? 1 : 0,
                    'dia3' => isset($_POST["dia3"]) ? 1 : 0,
                    'dia4' => isset($_POST["dia4"]) ? 1 : 0,
                    'dia5' => isset($_POST["dia5"]) ? 1 : 0,
                    'dia6' => isset($_POST["dia6"]) ? 1 : 0,
                    'dia7' => isset($_POST["dia7"]) ? 1 : 0,
                    'dia8' => isset($_POST["dia8"]) ? 1 : 0,
                    'dia9' => isset($_POST["dia9"]) ? 1 : 0,
                    'dia10' => isset($_POST["dia10"]) ? 1 : 0,
                    'dia11' => isset($_POST["dia11"]) ? 1 : 0,
                    'dia12' => isset($_POST["dia12"]) ? 1 : 0,
                    'dia13' => isset($_POST["dia13"]) ? 1 : 0,
                    'dia14' => isset($_POST["dia14"]) ? 1 : 0,
                    'dia15' => isset($_POST["dia15"]) ? 1 : 0,
                    'dia16' => isset($_POST["dia16"]) ? 1 : 0,
                    'dia17' => isset($_POST["dia17"]) ? 1 : 0,
                    'dia18' => isset($_POST["dia18"]) ? 1 : 0,
                    'dia19' => isset($_POST["dia19"]) ? 1 : 0,
                    'dia20' => isset($_POST["dia20"]) ? 1 : 0,
                    'dia21' => isset($_POST["dia21"]) ? 1 : 0,
                    'dia22' => isset($_POST["dia22"]) ? 1 : 0,
                    'dia23' => isset($_POST["dia23"]) ? 1 : 0,
                    'dia24' => isset($_POST["dia24"]) ? 1 : 0,
                    'dia25' => isset($_POST["dia25"]) ? 1 : 0,
                    'dia26' => isset($_POST["dia26"]) ? 1 : 0,
                    'dia27' => isset($_POST["dia27"]) ? 1 : 0,
                    'dia28' => isset($_POST["dia28"]) ? 1 : 0,
                    'dia29' => isset($_POST["dia29"]) ? 1 : 0,
                    'dia30' => isset($_POST["dia30"]) ? 1 : 0,
                    'dia31' => isset($_POST["dia31"]) ? 1 : 0,
                ];
            }

            if (empty($nombre)) {
                $error = "El nombre es obligatorio";

            } elseif (!in_array(1, $dias, true)) {
                $error = "Debes seleccionar al menos un día.";

            } else {
                $resultado = $this->tareaModel->crearTarea($idUsuario, $nombre, $descripcion, $esPosponible, $esSemanal, $dias);

                if ($resultado) {
                    $error = "Tarea creada correctamente";
                } else {
                    $error = "Error al guardar la tarea";
                }
            }
        }

        require __DIR__ . "/../views/tareas/crear_tarea.php";
    }

    /**
     * Actualiza el estado del cumplimiento temporal de una tarea.
     */
    public function guardadoTemporal()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $idUsuario = $_SESSION["idUsuario"];
            $idTarea = $_POST["idTarea"] ?? "";
            $estaCumplida = !empty($_POST["estaCumplida"]) ? 1 : 0;

            $resultado = $this->registroModel->actualizarRegistroTemporal($idTarea, $estaCumplida, $idUsuario);
            if (!$resultado) {
                die("Error al guardar el estado temporal.");
            }
        }
    }

    /*  LOS PASOS 2-4 SON UNA TRANSACCIÓN
    1. Son las 00:00
    2. guardadoPermanente()
    3. borrarRegistroTemporal()
    4. bucle de rellenarRegistroTemporal($idTarea) con las tareas del día nuevo
    5. Si el usuario está en hoy, se actualiza la vista para mostrar las tareas del día nuevo
    */

    /**
     * Realiza las acciones del cambio de día
     * 
     * 1. Crea los registros permanentes de las tareas del día que acaba de finalizar.
     * 2. Vacía la tabla de registros temporales.
     * 3. Crea los registros temporales para las tareas del nuevo día.
     * 
     * @throws Exception Si se produce un error durante el cambio de día.
     */
    public function cambioDeDia()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }
        $idUsuario = $_SESSION["idUsuario"];
        $this->conn->begin_transaction();

        try {
            if (!$this->guardadoPermanente()) {
                throw new Exception("Error al hacer el guardado permanente");
            }
            if (!$this->registroModel->borrarRegistroTemporal()) {
                throw new Exception("Error al borrar el registro temporal");
            }

            foreach ($this->tareaModel->obtenerIdsTareasHoy($idUsuario) as $tarea) {
                if (!$this->registroModel->prepararRegistroTemporal($tarea['idTarea'])) {
                    throw new Exception("Error al preparar el registro temporal");
                }
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            die("Error en el cambio de día: " . $e->getMessage());
        }
    }

    /**
     * Crea los registros permanentes de las tareas del día que acaba de finalizar.
     * 
     * Actualiza los puntos, la racha y el estado de las tareas.
     * 
     * @return bool Indica si se han creado los registros correctamente.
     */
    public function guardadoPermanente()
    {
        if (!isset($_SESSION["idUsuario"])) {
            return false;
        }
        $idUsuario = $_SESSION["idUsuario"];
        foreach ($this->tareaModel->obtenerParaGuardar($idUsuario) as $tarea) {
            if ($this->registroModel->obtenerRegistroTemporal($tarea['idTarea'])) {
                if ($tarea['esHabito']) {
                    $puntos = self::PUNTOS_HABITO;
                } else {
                    $puntos = $tarea['racha'];
                    $historial = $this->registroModel->obtenerRegistrosTarea($tarea['idTarea']);
                    $contCumplido = 0;
                    $contador = 0;
                    foreach ($historial as $registro) {
                        if ($registro["estado"] === 'P') {
                            continue;
                        }
                        $contador++;
                        if ($registro["estado"] === 'C') {
                            $contCumplido++;
                        }
                        if ($contador == 10) {
                            break;
                        }
                    }
                    if (($tarea['puntosAcumulados'] + $puntos) >= self::MIN_PUNTOS_HABITO && $contCumplido >= self::MIN_CUMPLIMIENTO_HABITO) {
                        if (!$this->tareaModel->convertirEnHabito($tarea['idTarea'])) {
                            return false;
                        }
                    }
                }
                if (!$this->registroModel->crearRegistroPermanente($tarea['idTarea'], 'C', $puntos)) {
                    return false;
                }
                if (!$this->tareaModel->actualizarCumplida($tarea['idTarea'], $puntos)) {
                    return false;
                }

            } else {

                if (!$tarea['esPosponible'] || !$this->tareaModel->mananaSeCumple($tarea['idTarea'])) {
                    if (!$this->registroModel->crearRegistroPermanente($tarea['idTarea'], 'F', 0)) {
                        return false;
                    }
                    if (!$this->tareaModel->reiniciarRacha($tarea['idTarea'])) {
                        return false;
                    }
                } else {
                    if (!$this->registroModel->crearRegistroPermanente($tarea['idTarea'], 'P', 0)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Muestra las tareas del día
     */
    public function hoy()
    {
        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }
        $idUsuario = $_SESSION["idUsuario"];

        $tareas = $this->tareaModel->obtenerParaMostrar($idUsuario);

        foreach ($tareas as &$tarea) {
            $tarea["estaCumplida"] = $this->registroModel->obtenerRegistroTemporal($tarea["idTarea"]);
        }

        require __DIR__ . "/../views/tareas/hoy.php";
    }

}