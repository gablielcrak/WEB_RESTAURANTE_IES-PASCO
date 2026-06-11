<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo   = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $fecha    = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $hora     = isset($_POST['hora']) ? $_POST['hora'] : '';
    $personas = isset($_POST['personas']) ? intval($_POST['personas']) : 0;

    if (empty($nombre) || empty($correo) || empty($fecha) || $personas <= 0) {
        echo "<script>alert('🚨 Por favor rellenar campos obligatorios.'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO reservas (nombre_cliente, correo, telefono, fecha_reserva, hora_reserva, numero_personas) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssi", $nombre, $correo, $telefono, $fecha, $hora, $personas);
        if ($stmt->execute()) {
            echo "<script>
                    alert('✅ ¡Mesa reservada con éxito!');
                    window.location.href = 'index.html';
                  </script>";
        } else {
            echo "❌ Error al reservar: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    header("Location: index.html");
    exit;
}
$conexion->close();
?>