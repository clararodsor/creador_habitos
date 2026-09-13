<?php
/**
 * Modelo encargado de gestionar los usuarios.
 *
 * Proporciona métodos para crear usuarios, obtener los usuarios,
 * obtener la contraseña de un usuario y cambiarla.
 */
class Usuario
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    /**
     * Crea un usuario.
     * 
     * @param string $nombre Nombre del usuario.
     * @param string $contrasenna Contraseña del usuario.
     * @return int|false Identificador del usuario creado o false si no se pudo crear.
     */
    public function crearUsuario($nombre, $contrasenna)
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

    /**
     * Obtiene el id de usuario y la contraseña de un usuario.
     * 
     * @param string $nombre Nombre del usuario.
     * @return array|null Datos del usuario o null si no existe.
     */
    public function buscarUsuarioPorNombre($nombre)
    {
        $stmt = $this->conn->prepare("SELECT idUsuario, contrasenna FROM usuarios WHERE nombre = ? ");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    /**
     * Obtiene la contraseña de un usuario.
     * 
     * @param int $idUsuario Identificador del usuario
     * @return string Contraseña del usuario.
     */
    public function obtenerContrasenna($idUsuario)
    {
        $stmt = $this->conn->prepare("SELECT contrasenna FROM usuarios WHERE idUsuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();
        return $usuario["contrasenna"];
    }

    /**
     * Cambia la contraseña de un usuario.
     * 
     * @param int $idUsuario Identificador del usuario.
     * @param string $contrasennaNueva La nueva contraseña del usuario.
     * @return int El número de filas que se han cambiado.
     */
    public function cambiarContrasenna($idUsuario, $contrasennaNueva)
    {
        $hash = password_hash($contrasennaNueva, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE  usuarios SET  contrasenna  =  ? WHERE  idUsuario = ? ");
        $stmt->bind_param("si", $hash, $idUsuario);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}