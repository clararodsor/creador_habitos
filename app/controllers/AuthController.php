<?php

require_once __DIR__ . "/../models/Usuario.php";

class AuthController
{
    private $usuarioModel;

    public function __construct($conn)
    {
        $this->usuarioModel = new Usuario($conn);
    }

    public function crearCuenta()
    {
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $nombre = trim($_POST["nombre"] ?? "");
            $contrasenna = $_POST["contrasenna"] ?? "";
            $confirmarContrasenna = $_POST["confirmarContrasenna"] ?? "";

            if (empty($nombre) || empty($contrasenna) || empty($confirmarContrasenna)) {
                $error = "Rellena todos los campos";
            } elseif ($this->usuarioModel->buscarPorNombre($nombre)) {
                $error = "El usuario ya existe";
            } elseif ($contrasenna !== $confirmarContrasenna) {
                $error = "Las contraseñas no coinciden";

            } else {
                $idUsuario = $this->usuarioModel->crear(
                    $nombre,
                    $contrasenna
                );

                if ($idUsuario !== false) {

                    $_SESSION["idUsuario"] = $idUsuario;
                    $_SESSION["nombre"] = $nombre;

                    header("Location: /creador_habitos/public/index.php?accion=hoy");
                    exit;

                } else {
                    $error = "Error al crear el usuario";
                }
            }
        }

        require __DIR__ . "/../views/auth/crear_cuenta.php";
    }

    public function login()
    {
        $error = null;
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = trim($_POST["nombre"] ?? "");
            $contrasenna = $_POST["contrasenna"] ?? "";
            if (!empty($nombre) && !empty($contrasenna)) {
                $usuario = $this->usuarioModel->buscarPorNombre($nombre);
                if ($usuario) {
                    if (password_verify($contrasenna, $usuario["contrasenna"])) {
                        $_SESSION["idUsuario"] = $usuario["idUsuario"];
                        $_SESSION["nombre"] = $nombre;
                        header("Location: /creador_habitos/public/index.php?accion=hoy");
                        exit;
                    } else {
                        $error = "Contraseña incorrecta";
                    }
                } else {
                    $error = "Usuario no encontrado";
                }
            } else {
                $error = "Rellena todos los campos";
            }
        }
        require __DIR__ . "/../views/auth/login.php";
    }

    public function logout()
    {
        session_destroy();

        header("Location: /creador_habitos/public/index.php?accion=login");
        exit;
    }

    public function cambiarContrasenna()
    {
        $error = null;

        if (!isset($_SESSION["idUsuario"])) {
            header("Location: /creador_habitos/public/index.php?accion=login");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nuevaContrasenna = $_POST["nuevaContrasenna"] ?? "";
            $confirmarNuevaContrasenna = $_POST["confirmarNuevaContrasenna"] ?? "";

            if (empty($nuevaContrasenna) || empty($confirmarNuevaContrasenna)) {
                $error = "Rellena todos los campos";
            } elseif ($nuevaContrasenna !== $confirmarNuevaContrasenna) {
                $error = "Las contraseñas no coinciden";
            } elseif (password_verify($nuevaContrasenna, $this->usuarioModel->obtenerContrasenna($_SESSION["idUsuario"]))) {
                $error = "No se ha cambiado la contraseña";
            } else {
                $numFilas = $this->usuarioModel->cambiarContrasenna($_SESSION["idUsuario"], $nuevaContrasenna);
                if ($numFilas === 1) {
                    $error = "Se ha cambiado la contraseña correctamente";
                } else {
                    $error = "Ha ocurrido un error";
                }
            }
        }
        require __DIR__ . "/../views/auth/cambiar_contrasenna.php";
    }
}