<?php
// 1. Incluir la conexión obligatoria a la base de datos
require_once 'conexion.php';

// 2. Capturar el número de mesa desde la URL (enviado por consultar_cuenta.html)
$mesa_buscada = isset($_GET['mesa']) ? intval($_GET['mesa']) : 0;

// Si no se pasó una mesa válida, detenemos la ejecución con un mensaje limpio
if ($mesa_buscada <= 0) {
    echo "<script>alert('🚨 Número de mesa no válido.'); window.location.href='consultar_cuenta.html';</script>";
    exit();
}

// 3. Consulta SQL (JOIN) para obtener el desglose de platos de la mesa y sus precios reales
$sql_desglose = "SELECT p.item_pedido, p.cantidad, pl.precio, (p.cantidad * pl.precio) AS subtotal 
                 FROM pedidos p 
                 JOIN platillos pl ON p.item_pedido = pl.nombre 
                 WHERE p.numero_mesa = ? 
                 ORDER BY p.id ASC";

$stmt_des = $conexion->prepare($sql_desglose);
$stmt_des->bind_param("i", $mesa_buscada);
$stmt_des->execute();
$resultado_des = $stmt_des->get_result();

// Si la mesa no tiene consumos registrados, alertamos al usuario y lo regresamos
if ($resultado_des->num_rows == 0) {
    echo "<script>alert('Esta mesa no registra consumos pendientes en este momento.'); window.location.href='consultar_cuenta.html';</script>";
    exit();
}

// 4. Consulta SQL secundaria para calcular la sumatoria total del consumo de la mesa
$sql_total = "SELECT SUM(p.cantidad * pl.precio) AS total_final 
              FROM pedidos p 
              JOIN platillos pl ON p.item_pedido = pl.nombre 
              WHERE p.numero_mesa = ?";
$stmt_tot = $conexion->prepare($sql_total);
$stmt_tot->bind_param("i", $mesa_buscada);
$stmt_tot->execute();
$resultado_tot = $stmt_tot->get_result();
$fila_total = $resultado_tot->fetch_assoc();
$total_cuenta = $fila_total['total_final'];

// Aseguramos la codificación correcta para caracteres especiales (ñ, acentos)
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo Mesa #<?php echo $mesa_buscada; ?> - Sabores del Mundo</title>
    <style>
        /* --- ESTILOS VISUALES DE LA PANTALLA --- */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            padding: 40px 20px; 
            color: #333; 
            background-color: #fafafa; 
        }
        .ticket-box { 
            background: #ffffff; 
            max-width: 550px; 
            margin: 0 auto; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 25px rgba(0,0,0,0.06); 
            border-top: 6px solid #d4af37; 
        }
        .header-ticket { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 20px; 
            margin-bottom: 25px; 
        }
        .logo-restaurante { 
            font-size: 24px; 
            font-weight: bold; 
            color: #111; 
        }
        .slogan { 
            font-size: 12px; 
            color: #777; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .titulo-documento { 
            font-size: 16px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #111; 
            text-align: right; 
        }
        .datos-meta { 
            font-size: 13px; 
            color: #555; 
            margin-top: 5px; 
            text-align: right; 
            line-height: 1.4;
        }
        .info-mesa-block { 
            background-color: #f9f9f9; 
            border-left: 4px solid #111; 
            padding: 15px; 
            margin-bottom: 25px; 
            font-size: 14px; 
        }
        .info-line { margin: 4px 0; }
        
        /* Tabla de consumo */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
        }
        th { 
            background-color: #111; 
            color: #fff; 
            padding: 12px 10px; 
            font-size: 11px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            text-align: left; 
        }
        td { 
            padding: 14px 10px; 
            border-bottom: 1px solid #eee; 
            font-size: 14px; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Bloque de Totales */
        .bloque-totales { 
            width: 50%; 
            margin-left: auto; 
        }
        .fila-total td { 
            font-size: 20px; 
            font-weight: bold; 
            border-top: 2px dashed #d4af37; 
            padding-top: 15px; 
            color: #d4af37; 
        }

        /* Botonera de interacción */
        .area-botones { 
            text-align: center; 
            margin-top: 40px; 
            border-top: 1px solid #eee; 
            padding-top: 20px;
        }
        .btn-print { 
            background-color: #111; 
            color: #fff; 
            border: none; 
            padding: 12px 25px; 
            font-weight: bold; 
            font-size: 14px; 
            border-radius: 6px; 
            cursor: pointer; 
            transition: background 0.3s; 
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-print:hover { 
            background-color: #d4af37; 
            color: #111; 
        }

        /* --- CONFIGURACIÓN DE IMPRESIÓN / GUARDADO PDF NATIVO --- */
        @media print {
            body { 
                background: none; 
                padding: 0; 
            }
            .ticket-box { 
                box-shadow: none; 
                max-width: 100%; 
                padding: 0; 
                border-top: none;
            }
            .area-botones { 
                display: none; /* Oculta los botones en el PDF final impreso */
            }
        }
    </style>
</head>
<body>

<div class="ticket-box">
    
    <div style="height: 4px; background: #d4af37; margin: -40px -40px 30px -40px; border-radius: 12px 12px 0 0;"></div>

    <div class="header-ticket">
        <div>
            <div class="logo-restaurante">🍳 Sabores del Mundo</div>
            <div class="slogan">Alta Cocina en tu Mesa</div>
        </div>
        <div>
            <div class="titulo-documento">Pre-Cuenta de Consumo</div>
            <div class="datos-meta">
                <strong>Fecha:</strong> <?php echo date("d/m/Y"); ?><br>
                <strong>Hora:</strong> <?php echo date("H:i"); ?>
            </div>
        </div>
    </div>

    <div class="info-mesa-block">
        <div class="info-line"><strong>Servicio:</strong> Consumo en Comedor</div>
        <div class="info-line"><strong>Identificador:</strong> Mesa N° <?php echo $mesa_buscada; ?></div>
        <div class="info-line"><strong>Estado:</strong> Documento informativo antes del pago</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Platillo Solicitado</th>
                <th class="text-center" style="width: 15%;">Cant.</th>
                <th class="text-right" style="width: 20%;">Precio U.</th>
                <th class="text-right" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado_des->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['item_pedido']); ?></td>
                    <td class="text-center"><?php echo $fila['cantidad']; ?></td>
                    <td class="text-right">$<?php echo number_format($fila['precio'], 2); ?></td>
                    <td class="text-right"><strong>$<?php echo number_format($fila['subtotal'], 2); ?></strong></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <table class="bloque-totales">
        <tr>
            <td class="text-right" style="color: #666; font-size: 14px;">Total Neto:</td>
            <td class="text-right" style="font-weight: 600; font-size: 14px;">$<?php echo number_format($total_cuenta, 2); ?></td>
        </tr>
        <tr class="fila-total">
            <td class="text-right">Total cobro:</td>
            <td class="text-right">$<?php echo number_format($total_cuenta, 2); ?></td>
        </tr>
    </table>

    <div class="area-botones">
        <p style="font-size: 12px; color: #888; margin-bottom: 20px;">Presiona el botón de abajo para generar tu PDF o imprimir el ticket físico desde caja.</p>
        <button onclick="window.print();" class="btn-print">🖨️ Guardar PDF / Imprimir</button>
        <a href="menu.html" class="btn-print" style="background-color: #666;">Volver al Menú</a>
    </div>

</div>

</body>
</html>
<?php 
// 5. Cerrar de manera limpia los flujos abiertos y la conexión al servidor de base de datos
$stmt_des->close();
$stmt_tot->close();
$conexion->close(); 
?>