<?php

class Tarea
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function crear(
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
    ) {
        $stmt = $this->conn->prepare("
            INSERT INTO tareas
            (idUsuario, nombre, descripcion, mon, tue, wed, thu, fri, sat, sun)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issiiiiiii",
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

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    public function obtenerPorUsuario($idUsuario)
    {
        $stmt = $this->conn->prepare(" SELECT idTarea, nombre, racha, puntosAcumulados, esHabito FROM tareas WHERE idUsuario = ? ");
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

    public function obtenerParaHoy($idUsuario, $diaSemanaHoy)
    {
        $stmt = $this->conn->prepare(" SELECT idTarea, nombre, descripcion, puntosAcumulados, racha, esHabito FROM tareas WHERE idUsuario = ? AND ( $diaSemanaHoy = 1 OR idTarea IN ( SELECT idTarea FROM registros WHERE fecha = (CURRENT_DATE - INTERVAL 1 DAY) AND pospuesto = 1 ) ) ");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tareas;
    }

    public function obtenerParaGuardar($idUsuario, $diaSemanaHoy)
    {
        $diasPermitidos = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
        if (!in_array($diaSemanaHoy, $diasPermitidos)) {
            return [];
        }
        $stmt = $this->conn->prepare(" SELECT idTarea, esHabito, racha, puntosAcumulados, mon, tue, wed, thu, fri, sat, sun FROM tareas WHERE idUsuario = ? AND ( $diaSemanaHoy = 1 OR idTarea IN ( SELECT idTarea FROM registros WHERE fecha = (CURRENT_DATE - INTERVAL 1 DAY) AND pospuesto = 1 ) ) ");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tareas;
    }
    public function actualizarCumplida($idTarea, $puntos)
    {
        $stmt = $this->conn->prepare(" UPDATE tareas SET racha = racha + 1, puntosAcumulados = puntosAcumulados + ? WHERE idTarea = ? ");
        $stmt->bind_param("ii", $puntos, $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    public function reiniciarRacha($idTarea)
    {
        $stmt = $this->conn->prepare(" UPDATE tareas SET racha = 0 WHERE idTarea = ? ");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    public function convertirEnHabito($idTarea)
    {
        $stmt = $this->conn->prepare(" UPDATE tareas SET esHabito = 1 WHERE idTarea = ? ");
        $stmt->bind_param("i", $idTarea);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

}