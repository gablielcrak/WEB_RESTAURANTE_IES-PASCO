<?php
require_once 'conexion.php';

// Obtener el número de mesa de la URL
$id_mesa = isset($_GET['mesa']) ? intval($_GET['mesa']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Mesa <?php echo $id_mesa; ?> - Sabores del Mundo</title>
    <link rel="stylesheet" href="CSS/global.css">
    <link rel="stylesheet" href="CSS/contacto.css"> 
    <style>
        /* Estilos personalizados del diseño elegante de la tarjeta */
        .ticket-item {
            background-color: #262626;
            border: 1px solid #333;
            border-left: 4px solid #d4af37;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-item .info-plato {
            display: flex;
            flex-direction: column;
        }
        .ticket-item .nombre-p {
            font-weight: bold;
            color: #fff;
            font-size: 15px;
        }
        .ticket-item .cant-p {
            color: #888;
            font-size: 13px;
            margin-top: 2px;
        }
        .ticket-item .precio-p {
            color: #d4af37;
            font-weight: bold;
            font-size: 15px;
        }
        .total-box {
            background: #262626;
            border: 1px solid #d4af37;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 25px;
        }
        .total-box span {
            display: block;
            color: #888;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .total-box strong {
            color: #d4af37;
            font-size: 24px;
        }
        .btn-accion-cuenta {
            display: block;
            width: 100%;
            background-color: #d4af37;
            color: #000;
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            box-sizing: border-box;
            transition: 0.3s;
            text-transform: uppercase;
        }
        .btn-accion-cuenta:hover {
            background-color: #bfa030;
        }
        .btn-secundario {
            background-color: transparent;
            color: #aaa;
            border: 1px solid #444;
            margin-top: 12px;
        }
        .btn-secundario:hover {
            background-color: #222;
            color: #fff;
        }
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
                <li><a href="consultar_cuenta.html" class="active">Ver Mi Cuenta</a></li>
                <li><a href="contacto.html" class="btn-nav">Reservar Mesa</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content" style="padding: 40px 20px; display: flex; justify-content: center;">
        <div class="container" style="max-width: 600px; margin: 0 auto; background: #1e1e1e; padding: 25px; border-radius: 8px; border: 1px solid #d4af37; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
            <h2 style="text-align: center; color: #d4af37; font-size: 28px; margin-bottom: 5px; text-transform: uppercase;">🧾 Estado de Cuenta</h2>
            <p style="text-align: center; color: #888; font-size: 14px; margin-bottom: 25px;">Consumo actual registrado para la Mesa <?php echo $id_mesa; ?></p>

            <?php
            if ($id_mesa <= 0) {
                echo "<p style='text-align:center; color:#ff8888; font-weight: bold;'>🚨 Número de mesa inválido.</p>";
            } else {
                // CONSULTA CORREGIDA COMPLETAMENTE: p.nombre_platillo y pe.estado
                $query = "SELECT p.nombre_platillo, dp.cantidad, p.precio, dp.subtotal 
                          FROM detalle_pedidos dp
                          INNER JOIN pedidos pe ON dp.id_pedido = pe.id_pedido
                          INNER JOIN platillos p ON dp.id_platillo = p.id_platillo
                          WHERE pe.id_mesa = ? AND pe.estado = 'Pendiente'";
                
                $stmt = $conexion->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("i", $id_mesa);
                    $stmt->execute();
                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0) {
                        $total_acumulado = 0;
                        
                        while ($fila = $resultado->fetch_assoc()) {
                            $total_acumulado += $fila['subtotal'];
                            echo "<div class='ticket-item'>
                                    <div class='info-plato'>
                                        <span class='nombre-p'>" . htmlspecialchars($fila['nombre_platillo']) . "</span>
                                        <span class='cant-p'>Cantidad: " . $fila['cantidad'] . " &bull; Precio: S/. " . number_format($fila['precio'], 2) . "</span>
                                    </div>
                                    <div class='precio-p'>S/. " . number_format($fila['subtotal'], 2) . "</div>
                                  </div>";
                        }
                        
                        echo "<div class='total-box'>
                                <span>Monto Total a Liquidar</span>
                                <strong>S/. " . number_format($total_acumulado, 2) . "</strong>
                              </div>";

                        ?>
                        <div style="margin-top: 15px;">
                            <a href="liberar_mesa.php?mesa=<?php echo $id_mesa; ?>" 
                               onclick="return confirm('¿Confirmas que la cuenta está pagada? Esto liberará la Mesa <?php echo $id_mesa; ?>.');" 
                               class="btn-accion-cuenta">
                               💵 Pagar y Liberar Mesa
                            </a>
                        </div>
                        <?php
                    } else {
                        echo "<p style='text-align: center; color: #aaa; padding: 25px; font-style: italic; background: #262626; border-radius: 6px; border: 1px dashed #444;'>🎉 Esta mesa se encuentra libre o no registra pedidos pendientes.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='text-align:center; color:#ff8888; font-weight: bold;'>🚨 Error de Sintaxis SQL: " . $conexion->error . "</p>";
                }
            }
            $conexion->close();
            ?>

            <a href="consultar_cuenta.html" class="btn-accion-cuenta btn-secundario">← Volver Atrás</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Restaurante Sabores del Mundo. Todos los derechos reservados.</p>
    </footer>

</body>
</html>