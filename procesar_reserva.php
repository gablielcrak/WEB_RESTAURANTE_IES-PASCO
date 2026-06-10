<?php
// 1. Incluir el archivo de conexión que creamos antes
include("conexion.php");

// 2. Verificar que los datos hayan sido enviados a través del método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Recibir los datos del formulario usando los atributos 'name' del HTML
    // mysqli_real_escape_string protege tu base de datos de hackeos (Inyección SQL)
    $nombre = $conexion->real_escape_string($_POST['nombre_completo']);
    $email  = $conexion->real_escape_string($_POST['email']);
    $fecha  = $conexion->real_escape_string($_POST['fecha']);
    $hora   = $conexion->real_escape_string($_POST['hora']);
    $personas = intval($_POST['num_personas']); // Convertir a número entero por seguridad

    // 4. Preparar la orden SQL para insertar en la tabla sin comillas invertidas
    $sql = "INSERT INTO reservas (nombre_completo, email, fecha, hora, num_personas) 
            VALUES ('$nombre', '$email', '$fecha', '$hora', $personas)";

    // 5. Ejecutar la orden y comprobar si se guardó con éxito
    if ($conexion->query($sql) === TRUE) {
        // Alerta de éxito en JavaScript y redirección automática al inicio
        echo "<script>
                alert('¡Reserva confirmada con éxito! Te esperamos.');
                window.location.href = 'index.html';
              </script>";
    } else {
        echo "Error al registrar la reserva: " . $conexion->error;
    }

    // 6. Cerrar la conexión para liberar memoria del servidor
    $conexion->close();
} else {
    // Si alguien intenta entrar a este archivo directamente sin llenar el formulario, lo mandamos al inicio
    header("Location: index.html");
    exit();
}
?>