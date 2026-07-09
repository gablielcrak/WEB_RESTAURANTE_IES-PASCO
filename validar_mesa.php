<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mesa     = intval($_POST['id_mesa']);
    $id_platillo = intval($_POST['id_platillo']);
    $cantidad    = intval($_POST['cantidad']);

    // 1. REGLA DE ESTABILIDAD: Verificar si la mesa ya está ocupada
    $stmt_verificar = $conexion->prepare("SELECT estado FROM mesas WHERE id_mesa = ?");
    $stmt_verificar->bind_param("i", $id_mesa);
    $stmt_verificar->execute();
    $resultado_mesa = $stmt_verificar->get_result()->fetch_assoc();

    if ($resultado_mesa && $resultado_mesa['estado'] === 'Ocupada') {
        echo "<script>
                alert('🚨 ¡Mesa Ocupada! La Mesa " . $id_mesa . " ya se encuentra registrada con un pedido en curso.');
                window.location.href = 'menu.html';
              </script>";
        $stmt_verificar->close();
        $conexion->close();
        exit();
    }

    // 2. Obtener el precio unitario del platillo seleccionado
    $stmt_precio = $conexion->prepare("SELECT precio FROM platillos WHERE id_platillo = ?");
    $stmt_precio->bind_param("i", $id_platillo);
    $stmt_precio->execute();
    $resultado_precio = $stmt_precio->get_result();
    
    if ($resultado_precio->num_rows > 0) {
        $platillo = $resultado_precio->fetch_assoc();
        $precio_base = $platillo['precio'];
        $subtotal = $precio_base * $cantidad;

        // 3. Insertar la cabecera del pedido
        $stmt_pedido = $conexion->prepare("INSERT INTO pedidos (id_mesa, total_pago, estado_pedido) VALUES (?, ?, 'Pendiente')");
        $stmt_pedido->bind_param("id", $id_mesa, $subtotal);
        $stmt_pedido->execute();
        $id_pedido_generado = $conexion->insert_id;

        // 4. Insertar el cuerpo/detalle del pedido
        $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedidos (id_pedido, id_platillo, cantidad, subtotal) VALUES (?, ?, ?, ?)");
        $stmt_detalle->bind_param("iiid", $id_pedido_generado, $id_platillo, $cantidad, $subtotal);
        $stmt_detalle->execute();

        // 5. Actualizar el estado de la mesa a 'Ocupada'
        $stmt_mesa = $conexion->prepare("UPDATE mesas SET estado = 'Ocupada' WHERE id_mesa = ?");
        $stmt_mesa->bind_param("i", $id_mesa);
        $stmt_mesa->execute();

        echo "<script>
                alert('🚀 ¡Comanda enviada con éxito! La Mesa " . $id_mesa . " ahora está Ocupada.');
                window.location.href = 'menu.html';
              </script>";
    } else {
        echo "<script>alert('Error: El platillo seleccionado no existe.'); window.location.href = 'menu.html';</script>";
    }

    $stmt_verificar->close();
    $stmt_precio->close();
    $conexion->close();
}
?>