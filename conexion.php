<?php
// Configuración de las credenciales de XAMPP
$servidor = "localhost";
$usuario  = "root";
$password = ""; // En XAMPP por defecto viene vacío
$base_datos = "bd_restaurante";

// Crear la conexión utilizando la librería mysqli
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar si la conexión falló
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

// Configurar para que acepte tildes y eñes correctamente al insertar datos
$conexion->set_charset("utf8mb4");
?>