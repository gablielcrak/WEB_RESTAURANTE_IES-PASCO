<?php
// Incluimos el puente de conexión
include("conexion.php");

// Recibimos de forma segura los parámetros desde el formulario
$numero_mesa = isset($_POST['numero_mesa']) ? $_POST['numero_mesa'] : '';
$item_pedido = isset($_POST['item_pedido']) ? $_POST['item_pedido'] : '';
$cantidad    = isset($_POST['cantidad']) ? $_POST['cantidad'] : '';

// Doble validación de consistencia para el puntero de conexión
if (!isset($conex) && isset($conexion)) {
    $conex = $conexion;
}

if ($conex) {
    // Escapamos strings para evitar fallos por comillas o caracteres extraños
    $numero_mesa = mysqli_real_escape_string($conex, $numero_mesa);
    $item_pedido = mysqli_real_escape_string($conex, $item_pedido);
    $cantidad    = mysqli_real_escape_string($conex, $cantidad);

    // Consulta directa de inserción
    $query = "INSERT INTO pedidos (numero_mesa, item_pedido, cantidad) VALUES ('$numero_mesa', '$item_pedido', '$cantidad')";
    $resultado = mysqli_query($conex, $query);

    if ($resultado) {
        echo "<script>
                alert('¡Pedido enviado con éxito a la cocina para la Mesa $numero_mesa!');
                window.location.href = 'menu.html';
              </script>";
    } else {
        echo "Error al insertar el registro: " . mysqli_error($conex);
    }
} else {
    echo "Error de inicialización: El enlace con la base de datos es nulo. Verifica los parámetros de conexion.php.";
}
?>