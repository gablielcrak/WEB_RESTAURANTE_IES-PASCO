<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero_mesa = isset($_POST['numero_mesa']) ? intval($_POST['numero_mesa']) : 0;
    $item_pedido = isset($_POST['item_pedido']) ? trim($_POST['item_pedido']) : '';
    $cantidad    = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if ($numero_mesa <= 0 || empty($item_pedido) || $cantidad <= 0) {
        echo "<script>alert('🚨 Datos inválidos.'); window.location.href = 'menu.html';</script>";
        exit;
    }

    $sql = "INSERT INTO pedidos (numero_mesa, item_pedido, cantidad) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isi", $numero_mesa, $item_pedido, $cantidad);
        if ($stmt->execute()) {
            echo "<script>
                    alert('✅ ¡Pedido enviado para la Mesa " . $numero_mesa . "!');
                    window.location.href = 'menu.html';
                  </script>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    header("Location: menu.html");
    exit;
}
$conexion->close();
?>