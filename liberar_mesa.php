<?php
// 1. Incluir el archivo de conexión centralizado
require_once 'conexion.php';

// 2. Verificar que el número de mesa haya llegado correctamente por la URL
if (isset($_GET['mesa'])) {
    $id_mesa = intval($_GET['mesa']);

    if ($id_mesa > 0) {
        // Iniciar una transacción para asegurar integridad en ambas tablas
        $conexion->begin_transaction();

        try {
            // PASO A: Cambiar el estado de la mesa en la tabla 'mesas' a 'Disponible'
            $stmt_mesa = $conexion->prepare("UPDATE mesas SET estado = 'Disponible' WHERE id_mesa = ?");
            $stmt_mesa->bind_param("i", $id_mesa);
            $stmt_mesa->execute();
            $stmt_mesa->close();

            // PASO B: Cambiar el estado de los pedidos 'Pendientes' de esa mesa a 'Pagado'
            $stmt_pedido = $conexion->prepare("UPDATE pedidos SET estado_pedido = 'Pagado' WHERE id_mesa = ? AND estado_pedido = 'Pendiente'");
            $stmt_pedido->bind_param("i", $id_mesa);
            $stmt_pedido->execute();
            $stmt_pedido->close();

            // Si todo salió bien, confirmar los cambios
            $conexion->commit();

            // Mostrar mensaje de éxito y redireccionar
            echo "<script>
                    alert('💵 ¡Mesa " . $id_mesa . " liberada con éxito! Se ha registrado el pago de la cuenta.');
                    window.location.href = 'consultar_cuenta.html';
                  </script>";
            exit();

        } catch (\Exception $e) { 
            // Si algo falla en el proceso, deshacer los cambios parciales
            $conexion->rollback();
            echo "<script>
                    alert('🚨 Error del sistema al liberar la mesa. Inténtelo nuevamente.');
                    window.location.href = 'consultar_cuenta.html';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('🚨 Número de mesa inválido.');
                window.location.href = 'consultar_cuenta.html';
              </script>";
        exit();
    }
} else {
    header("Location: consultar_cuenta.html");
    exit();
}
?>