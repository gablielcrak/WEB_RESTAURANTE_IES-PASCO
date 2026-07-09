<?php
// Configuración de credenciales del servidor local XAMPP
$servidor = "localhost";
$usuario  = "root";
$password = "";
$base_datos = "sabores_del_mundo"; // Asegúrate de que coincida con el nombre de tu BD

// Crear la conexión utilizando la librería MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar si hubo un fallo de conexión
if ($conexion->connect_error) {
    die("🚨 Error crítico de conexión a la Base de Datos: " . $conexion->connect_error);
}

// Configurar el juego de caracteres a UTF-8 para admitir tildes y eñes correctamente
$conexion->set_charset("utf8");
?>