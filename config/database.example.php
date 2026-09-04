<?php
$host = "localhost";
$user = "root";
$pass = "contrasenna";
$db = "nombre_de_la_base_de_datos";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}