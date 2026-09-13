<?php

/**
 * Modelo encargado de gestionar los registros de las tareas de los usuarios.
 *
 * Tiene métodos para crear registros (temporales y permanentes),
 * modificar los registros temporales y consultar los registros.
 */
class Registro
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Crea un registro permanente.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @param string $estado Representa si la tarea se cumplió, pospuso o falló.
     * @param int $puntos Número de puntos obtenidos.
     * @return bool Indica si se creó el registro correctamente.
     */
    public function crearRegistroPermanente($idTarea, $estado, $puntos)
    {
        $stmt = $this->conn->prepare("INSERT INTO registros (idTarea, estado, puntos) VALUES (?, ?, ?) ");
        $stmt->bind_param("isi", $idTarea, $estado, $puntos);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Obtiene los estados de los registros de varias tareas.
     * 
     * @param array $idsTareas Array de identificadores de las tareas.
     * @return array Registros agrupados por identificador de tarea.
     */
    public function obtenerRegistrosPorTareas($idsTareas)
    {
        $registrosPorTarea = [];

        if (empty($idsTareas)) {
            return $registrosPorTarea;
        }

        $idsString = implode(',', $idsTareas);

        $sql = "SELECT idTarea, estado FROM registros WHERE idTarea IN ($idsString) ORDER BY fecha DESC";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $registrosPorTarea[$row['idTarea']][] = $row;
            }
        }

        return $registrosPorTarea;
    }

    /**
     * Obtiene los estados de los registros de una tarea.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return array Historial de los estados de la tarea.
     */
    public function obtenerRegistrosTarea($idTarea)
    {
        $stmt = $this->conn->prepare("SELECT estado FROM registros WHERE idTarea = ? ORDER BY fecha DESC ");
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $result = $stmt->get_result();
        $historial = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $historial;
    }

    /**
     * Vacía la tabla de registros temporales.
     * 
     * @return bool Indica si se vació la tabla correctamente.
     */
    public function borrarRegistroTemporal()
    {
        return $this->conn->query("DELETE FROM registros_temp");
    }

    /**
     * Inserta una tarea del día en la tabla de registros temporales.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si se insertó correctamente.
     */
    public function prepararRegistroTemporal($idTarea)
    {
        $stmt = $this->conn->prepare("INSERT INTO registros_temp (idTarea) VALUES (?)"); //estaCumplida es false por defecto
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Modifica el registro de una tarea en la tabla de registros temporales.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @param bool $estaCumplida Indica si la tarea se ha cumplido o no.
     * @param int $idUsuario Identificador del usuario.
     *@return bool Indica si se actualizó correctamente el registro.
     */
    public function actualizarRegistroTemporal($idTarea, $estaCumplida, $idUsuario)
    {
        $stmt = $this->conn->prepare("UPDATE registros_temp rt INNER JOIN tareas t ON rt.idTarea = t.idTarea SET rt.estaCumplida = ?
            WHERE rt.idTarea = ? AND t.idUsuario = ?");
        $stmt->bind_param("iii", $estaCumplida, $idTarea, $idUsuario);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Devuelve el registro temporal de una tarea.
     * 
     * @param int $idTarea Identificador de la tarea.
     * @return bool Indica si la tarea se cumplió o no.
     */
    public function obtenerRegistroTemporal($idTarea)
    {
        $stmt = $this->conn->prepare("SELECT estaCumplida FROM registros_temp WHERE idTarea = ?");
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $registro = $resultado->fetch_assoc();
        $stmt->close();
        return $registro ? $registro["estaCumplida"] : 0;
    }

}