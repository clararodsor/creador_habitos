<?php

class Usuario
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function crear($nombre, $contrasenna)
    {
        $hash = password_hash($contrasenna, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO usuarios (nombre, contrasenna) VALUES (?, ?)"
        );

        $stmt->bind_param("ss", $nombre, $hash);

        if ($stmt->execute()) {
            $idUsuario = $stmt->insert_id;
            $stmt->close();

            return $idUsuario;
        }

        $stmt->close();

        return false;
    }

    public function obtenerContrasenna($idUsuario)
    {
        $stmt = $this->conn->prepare(
            "SELECT contrasenna FROM usuarios WHERE idUsuario = ?"
        );

        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();

        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        $stmt->close();

        return $usuario["contrasenna"];
    }

    public function buscarPorNombre($nombre)
    {
        $stmt = $this->conn->prepare("SELECT idUsuario, contrasenna FROM usuarios WHERE nombre = ? ");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    public function cambiarContrasenna($idUsuario, $contrasennaNueva)
    {
        $hash = password_hash($contrasennaNueva, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE  usuarios SET  contrasenna  =  ? WHERE  idUsuario = ? ");
        $stmt->bind_param("si", $hash, $idUsuario);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}