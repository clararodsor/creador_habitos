<?php

session_start();

require_once "../config/database.php";

$accion = $_GET["accion"] ?? "login";

switch ($accion) {

    case "login":
        require_once "../app/controllers/AuthController.php";

        $controller = new AuthController($conn);
        $controller->login();
        break;

    case "crearCuenta":
        require_once "../app/controllers/AuthController.php";

        $controller = new AuthController($conn);
        $controller->crearCuenta();
        break;

    case "cambiarContrasenna":
        require_once "../app/controllers/AuthController.php";

        $controller = new AuthController($conn);
        $controller->cambiarContrasenna();
        break;

    case "logout":
        require_once "../app/controllers/AuthController.php";

        $controller = new AuthController($conn);
        $controller->logout();
        break;

    case "crearTarea":
        require_once "../app/controllers/TareaController.php";

        $controller = new TareaController($conn);
        $controller->crear();
        break;

    case "estadisticas":
        require_once "../app/controllers/EstadisticasController.php";

        $controller = new EstadisticasController($conn);
        $controller->mostrar();
        break;

    case "hoy":
        require_once "../app/controllers/TareaController.php";

        $controller = new TareaController($conn);
        $controller->hoy();
        break;

    default:
        http_response_code(404);
        echo "Página no encontrada";
        break;
}