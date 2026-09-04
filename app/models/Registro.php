<?php

class Registro
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function obtenerPorTareas($idsTareas)
    {
        $registrosPorTarea = [];

        if (empty($idsTareas)) {
            return $registrosPorTarea;
        }

        $idsString = implode(',', $idsTareas);

        $sql = "
            SELECT idTarea, cumplido, pospuesto
            FROM registros
            WHERE idTarea IN ($idsString)
            ORDER BY fecha DESC
        ";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $registrosPorTarea[$row['idTarea']][] = $row;
            }
        }

        return $registrosPorTarea;
    }

    public function existeRegistroHoy($idUsuario, $fecha)
    {
        $stmt = $this->conn->prepare(" SELECT 1 FROM registros r JOIN tareas t ON r.idTarea = t.idTarea WHERE t.idUsuario = ? AND r.fecha = ? LIMIT 1 ");
        $stmt->bind_param("is", $idUsuario, $fecha);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function obtenerHistorial($idTarea)
    {
        $stmt = $this->conn->prepare(" SELECT cumplido, pospuesto FROM registros WHERE idTarea = ? ORDER BY fecha DESC ");
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $result = $stmt->get_result();
        $historial = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $historial;
    }
    public function crear($idTarea, $cumplido, $pospuesto, $puntos)
    {
        $stmt = $this->conn->prepare(" INSERT INTO registros (idTarea, cumplido, pospuesto, puntos) VALUES (?, ?, ?, ?) ");
        $stmt->bind_param("iiii", $idTarea, $cumplido, $pospuesto, $puntos);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

}