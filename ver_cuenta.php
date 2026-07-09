<?php
require_once 'conexion.php';

// Obtener el número de mesa de la URL (por ejemplo: ver_cuenta.php?mesa=2)
$id_mesa = isset($_GET['mesa']) ? intval($_GET['mesa']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Mesa <?php echo $id_mesa; ?> - Sabores del Mundo</title>
    <link rel="stylesheet" href="CSS/global.css">
    <style>
        .cuenta-box { max-width: 650px; margin: 40px auto; background: #1e1e1e; border: 1px solid #d4af37; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .cuenta-box h2 { text-align: center; color: #d4af37; margin-bottom: 20px; text-transform: uppercase; }
        .table-cuenta { width: 100%; border-collapse: collapse; margin-top: 15px; color: #fff; }
        .table-cuenta th { border-bottom: 2px solid #d4af37; padding: 12px; text-align: left; color: #d4af37; }
        .table-cuenta td { padding: 12px; border-bottom: 1px solid #333; }
        .total-row { font-size: 18px; font-weight: bold; color: #d4af37; text-align: right; }
        .no-pedidos { text-align: center; color: #888; padding: 30px; font-style: italic; }
        .btn-volver { display: block; width: 150px; text-align: center; background: #d4af37; color: #111; padding: 10px; margin: 25px auto 0; text-decoration: none; font-weight: bold; border-radius: 4px; }
    </style>
</head>
<body>

    <header>
        <div class="logo"><h1>🍳 Sabores del Mundo</h1></div>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="sobre_el_restaurante.html">Nosotros</a></li>
                <li><a href="menu.html">Menú</a></li>
                <li><a href="galeria.html">Galería</a></li>
                <li><a href="eventos.html">Eventos</a></li>
                <li><a href="consultar_cuenta.html" class="active">🔍 Ver Mi Cuenta</a></li>
                <li><a href="contacto.html" class="btn-nav">Reservar Mesa</a></li>
            </ul>
        </nav>
    </header>

    <main class="cuenta-box">
        <h2>🧾 Estado de Cuenta: Mesa <?php echo $id_mesa; ?></h2>

        <?php
        if ($id_mesa <= 0) {
            echo "<p class='no-pedidos'>Número de mesa inválido.</p>";
        } else {
            // Consultar los platillos pedidos por esta mesa en estado Pendiente
            $query = "SELECT p.nombre, dp.cantidad, p.precio, dp.subtotal 
                      FROM detalle_pedidos dp
                      INNER JOIN pedidos pe ON dp.id_pedido = pe.id_pedido
                      INNER JOIN platillos p ON dp.id_platillo = p.id_platillo
                      WHERE pe.id_mesa = ? AND pe.estado_pedido = 'Pendiente'";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_mesa);
            $stmt->execute();
            $resultado = $stmt->get_get_result();

            if ($resultado->num_rows > 0) {
                echo "<table class='table-cuenta'>
                        <thead>
                            <tr>
                                <th>Platillo</th>
                                <th>Cant.</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>";
                
                $total_acumulado = 0;
                while ($fila = $resultado->fetch_assoc()) {
                    $total_acumulado += $fila['subtotal'];
                    echo "<tr>
                            <td>" . htmlspecialchars($fila['nombre']) . "</td>
                            <td>" . $fila['cantidad'] . "</td>
                            <td>S/. " . number_format($fila['precio'], 2) . "</td>
                            <td>S/. " . number_format($fila['subtotal'], 2) . "</td>
                          </tr>";
                }
                
                echo "<tr>
                        <td colspan='3' class='total-row'>Monto Total a Pagar:</td>
                        <td class='total-row'>S/. " . number_format($total_acumulado, 2) . "</td>
                      </tr>";
                echo "</tbody></table>";
            } else {
                echo "<p class='no-pedidos'>🎉 Esta mesa no tiene consumos registrados o su cuenta ya fue saldada.</p>";
            }
            $stmt->close();
        }
        $conexion->close();
        ?>

        <a href="consultar_cuenta.html" class="btn-volver">Regresar</a>
    </main>

    <footer>
        <p>&copy; 2026 Restaurante Sabores del Mundo. Todos los derechos reservados.</p>
    </footer>

</body>
</html>