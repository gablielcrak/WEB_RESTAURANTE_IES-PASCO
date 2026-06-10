<?php
// Incluir la conexión existente a la base de datos
include("conexion.php");

// Verificar que los datos lleguen por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Limpiar y recibir variables del formulario
    $numero_mesa = intval($_POST['numero_mesa']);
    $item_pedido = trim($_POST['item_pedido']);
    $cantidad    = intval($_POST['cantidad']);

    // Validar que los campos no estén vacíos o alterados
    if (!empty($numero_mesa) && !empty($item_pedido) && !empty($cantidad)) {
        
        // Usar Sentencias Preparadas para evitar Inyección SQL (Seguridad Premium)
        $sql = "INSERT INTO pedidos (numero_mesa, item_pedido, cantidad) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conex, $sql)) {
            // Unir los parámetros (i = int, s = string)
            mysqli_stmt_bind_param($stmt, "isi", $numero_mesa, $item_pedido, $cantidad);
            
            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                        alert('¡Pedido enviado con éxito! Su comida/bebida está en preparación para la mesa $numero_mesa.');
                        window.location.href = 'menu.html';
                      </script>";
            } else {
                echo "Error al procesar el pedido en la base de datos.";
            }
            
            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Por favor, rellene todos los campos del pedido correctamente.";
    }
}

// Cerrar la conexión
mysqli_close($conex);
?>