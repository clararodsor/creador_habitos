<?php
/**
 * Modelo encargado de gestionar las tareas de los usuarios.
 *
 * Tiene métodos para crear tareas, guardar su frecuencia de cumplimiento,
 * obtener las tareas y actualizar sus datos
 */

class Tarea
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Crea una tarea y guarda su frecuencia de cumplimiento.
     *
     * @param int $idUsuario Identificador del usuario.
     * @param string $nombre Nombre de la tarea.
     * @param string $descripcion Descripción de la tarea.
     * @param bool $esPosponible Indica si la tarea se puede posponer.
     * @param bool $esSemanal Indica si la tarea tiene frecuencia semanal.
     * @param array $dias Días (de la semana o del mes) en los que se debe cumplir la tarea.
     * @return bool Indica si la tarea se creó correctamente.
     */

    public function crearTarea(
        $idUsuario,
        $nombre,
        $descripcion,
        $esPosponible,
        $esSemanal,
        $dias
    ) {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("INSERT INTO tareas (idUsuario, nombre, descripcion, esPosponible, esSemanal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issii", $idUsuario, $nombre, $descripcion, $esPosponible, $esSemanal);
            if (!$stmt->execute()) {
                throw new Exception("Error al crear la tarea");
            }
            $idTarea = $stmt->insert_id;
            $stmt->close();

            if ($esSemanal) {
                if (!$this->guardarSemana($idTarea, $dias)) {
                    throw new Exception("Error al guardar los días");
                }
            } else {
                if (!$this->guardarMes($idTarea, $dias)) {
                    throw new Exception("Error al guardar los días");
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Almacena la frecuencia de cumplimiento de las tareas semanales.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @param array $dias Los días de la semana que se tiene que cumplir la tarea.
     * @return bool Indica si se guardó correctamente la frecuencia.
     */
    public function guardarSemana($idTarea, $dias)
    {
        $stmt = $this->conn->prepare("INSERT INTO semana (idTarea, mon, tue, wed, thu, fri, sat, sun) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiiiii", $idTarea, $dias['mon'], $dias['tue'], $dias['wed'], $dias['thu'], $dias['fri'], $dias['sat'], $dias['sun']);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Almacena la frecuencia de cumplimiento de las tareas mensuales.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @param array $dias Los días del mes que se tiene que cumplir la tarea.
     * @return bool Indica si se guardó correctamente la frecuencia.
     */
    public function guardarMes($idTarea, $dias)
    {
        $stmt = $this->conn->prepare("INSERT INTO mes
            (idTarea, dia1, dia2, dia3, dia4, dia5, dia6, dia7, dia8, dia9, dia10, dia11, dia12, dia13, dia14, dia15, dia16, dia17, dia18, dia19, dia20, dia21, dia22, dia23, dia24, dia25, dia26, dia27, dia28, dia29, dia30, dia31)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii",
            $idTarea,
            $dias['dia1'],
            $dias['dia2'],
            $dias['dia3'],
            $dias['dia4'],
            $dias['dia5'],
            $dias['dia6'],
            $dias['dia7'],
            $dias['dia8'],
            $dias['dia9'],
            $dias['dia10'],
            $dias['dia11'],
            $dias['dia12'],
            $dias['dia13'],
            $dias['dia14'],
            $dias['dia15'],
            $dias['dia16'],
            $dias['dia17'],
            $dias['dia18'],
            $dias['dia19'],
            $dias['dia20'],
            $dias['dia21'],
            $dias['dia22'],
            $dias['dia23'],
            $dias['dia24'],
            $dias['dia25'],
            $dias['dia26'],
            $dias['dia27'],
            $dias['dia28'],
            $dias['dia29'],
            $dias['dia30'],
            $dias['dia31']
        );
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Obtiene todas las tareas activas de un usuario
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Las tareas del usuario y los datos.
     */
    public function obtenerPorUsuario($idUsuario)
    {
        $stmt = $this->conn->prepare("SELECT idTarea, nombre, racha, puntosAcumulados, esHabito FROM tareas WHERE idUsuario = ? AND estaActiva = 1");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = [];
        while ($row = $result->fetch_assoc()) {
            $tareas[] = $row;
        }
        $stmt->close();
        return $tareas;
    }

    /**
     * Obtiene los ids de todas las tareas activas de hoy de un usuario
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Los ids de las tareas del usuario.
     */
    public function obtenerIdsTareasHoy($idUsuario)
    {
        $columnaMes = "dia" . date('j');
        $tareasSemanales = $this->obtenerIdsTareasSemanalesHoy($idUsuario, strtolower(date("D")));
        $tareasMensuales = $this->obtenerIdsTareasMensualesHoy($idUsuario, $columnaMes);
        return array_merge($tareasSemanales, $tareasMensuales);
    }

    /**
     * Obtiene los ids de todas las tareas semanales que se deben cumplir hoy.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Los ids de las tareas semanales del usuario.
     */
    public function obtenerIdsTareasSemanalesHoy($idUsuario, $diaSemanaHoy)
    {
        $stmt = $this->conn->prepare("SELECT t.idTarea FROM tareas t INNER JOIN semana s ON t.idTarea = s.idTarea WHERE t.idUsuario = ? AND t.esSemanal = 1 AND t.estaActiva = 1 AND ($diaSemanaHoy = 1 OR (t.idTarea IN (SELECT idTarea FROM registros WHERE fecha = (CURRENT_DATE - INTERVAL 1 DAY) AND estado = 'P')))");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tareas;
    }

    /**
     * Obtiene los ids de todas las tareas mensuales que se deben cumplir hoy.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Los ids de las tareas mensuales del usuario.
     */
    public function obtenerIdsTareasMensualesHoy($idUsuario, $diaMesHoy)
    {
        $stmt = $this->conn->prepare("SELECT t.idTarea FROM tareas t INNER JOIN mes m ON t.idTarea = m.idTarea WHERE t.idUsuario = ? AND t.esSemanal = 0 AND t.estaActiva = 1 AND ($diaMesHoy = 1 OR (t.idTarea IN (SELECT idTarea FROM registros WHERE fecha = (CURRENT_DATE - INTERVAL 1 DAY) AND estado = 'P')))");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tareas;
    }

    /**
     * Obtiene las tareas activas del usuario junto con la información necesaria para mostrarlas.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Datos de las tareas que se deben mostrar al usuario.
     */
    public function obtenerParaMostrar($idUsuario)
    {
        $idsTareas = $this->obtenerIdsTareasHoy($idUsuario);
        $ids = array_column($idsTareas, "idTarea");
        if (empty($ids)) {
            return [];
        }
        $idsString = implode(",", $ids);
        $result = $this->conn->query("SELECT idTarea, nombre, descripcion, puntosAcumulados, racha, esHabito FROM tareas WHERE idTarea IN ($idsString)");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene las tareas activas del usuario junto con la información necesaria para crear sus registros.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return array Datos de las tareas necesarios para crear sus registros.
     */
    public function obtenerParaGuardar($idUsuario)
    {
        $idsTareas = $this->obtenerIdsTareasHoy($idUsuario);
        $ids = array_column($idsTareas, "idTarea");
        if (empty($ids)) {
            return [];
        }
        $idsString = implode(",", $ids);

        $result = $this->conn->query("SELECT t.idTarea, t.esPosponible, t.esHabito, t.puntosAcumulados, t.racha,
            s.mon, s.tue, s.wed, s.thu, s.fri, s.sat, s.sun,
            m.dia1, m.dia2, m.dia3, m.dia4, m.dia5, m.dia6, m.dia7, m.dia8, m.dia9, m.dia10, m.dia11, m.dia12, m.dia13, m.dia14, m.dia15, m.dia16, m.dia17, m.dia18, m.dia19, m.dia20, m.dia21, m.dia22, m.dia23, m.dia24, m.dia25, m.dia26, m.dia27, m.dia28, m.dia29, m.dia30, m.dia31
            FROM tareas t LEFT JOIN semana s ON t.idTarea = s.idTarea LEFT JOIN mes m ON t.idTarea = m.idTarea WHERE t.idTarea IN ($idsString)");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todos los puntos obtenidos por el usuario.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @return int Suma de puntos.
     */
    public function obtenerSumaPuntos($idUsuario)
    {
        $stmt = $this->conn->prepare("SELECT SUM(puntosAcumulados) AS totalPuntos FROM tareas WHERE idUsuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        return $fila["totalPuntos"] ?? 0;
    }

    /**
     * Actualiza los datos de una tarea cuando se cumple.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @param int $puntos Los puntos que suma.
     * @return bool Indica si se actualizaron los datos correctamente.
     */
    public function actualizarCumplida($idTarea, $puntos)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET racha = racha + 1, puntosAcumulados = puntosAcumulados + ? WHERE idTarea = ?");
        $stmt->bind_param("ii", $puntos, $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Reinicia la racha a 0.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se actualizaron los datos correctamente.
     */
    public function reiniciarRacha($idTarea)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET racha = 0 WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Cambia el estado de una tarea a hábito.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se actualizaron los datos correctamente.
     */
    public function convertirEnHabito($idTarea)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET esHabito = 1 WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Cambia el estado de una tarea a propósito.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se actualizaron los datos correctamente.
     */
    public function deshacerHabito($idTarea)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET esHabito = 0, racha = 0 WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Marca una tarea como no activa, para eliminarla sin eliminar los registros.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se actualizaron los datos correctamente.
     */
    public function eliminarTarea($idTarea)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET estaActiva = 0 WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Indica si al día siguiente se tiene que cumplir la tarea o no.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se tiene que cumplir la tarea o no.
     */

    public function mananaSeCumple($idTarea)
    {
        $stmt = $this->conn->prepare("SELECT esSemanal FROM tareas WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $result = $stmt->get_result();
        $tarea = $result->fetch_assoc();
        $stmt->close();

        if ($tarea['esSemanal']) {
            $columna = strtolower(date("D", strtotime("+1 day")));
            $stmt = $this->conn->prepare("SELECT $columna FROM semana WHERE idTarea = ?");
        } else {
            $columna = "dia" . date("j", strtotime("+1 day"));
            $stmt = $this->conn->prepare("SELECT $columna FROM mes WHERE idTarea = ?");
        }
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $stmt->close();

        return (bool) array_values($fila)[0];
    }

}