<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre    = strip_tags(trim($_POST['nombre']));
    $correo    = strip_tags(trim($_POST['correo']));
    $telefono  = strip_tags(trim($_POST['telefono']));
    $fecha     = $_POST['fecha'];
    $hora      = $_POST['hora'];
    $personas  = intval($_POST['personas']);

    // Insertar la reservación en la base de datos de manera limpia (tabla reservaciones)
    $stmt = $conexion->prepare("INSERT INTO reservaciones (nombre_cliente, correo, telefono, fecha_reserva, hora_reserva, num_personas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nombre, $correo, $telefono, $fecha, $hora, $personas);

    if ($stmt->execute()) {
        echo "<script>
                alert('📅 ¡Reserva Confirmada! Gracias " . $nombre . ", los esperamos con gusto.');
                window.location.href = 'contacto.html';
              </script>";
    } else {
        echo "<script>
                alert('🚨 Hubo un error al procesar la reserva. Por favor intente de nuevo.');
                window.location.href = 'contacto.html';
              </script>";
    }

    $stmt->close();
    $conexion->close();
}
?>