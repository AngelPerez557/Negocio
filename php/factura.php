<?php
// factura.php
// Muestra una factura imprimible con detalles desde la base de datos
include '../php/conexion.php';

// Obtener el ID de la factura por GET o POST
$facturaID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($facturaID <= 0) {
    echo '<div style="color:red;">No se especificó una factura válida.</div>';
    exit;
}


// Obtener datos de la factura
$sqlFactura = "SELECT f.*, m.Descripcion as MetodoPago FROM facturas f LEFT JOIN metodopago m ON f.MetodoPagoID = m.MetodoPagoID WHERE f.FacturaID = $facturaID";
$resFactura = $conexion->query($sqlFactura);
$factura = $resFactura->fetch_assoc();
if (!$factura) {
    echo '<div style="color:red;">Factura no encontrada.</div>';
    exit;
}

// Obtener detalles de la factura (incluye si es gravado)
$sqlDetalles = "SELECT d.*, a.nombre, a.es_gravado FROM facturadetalle d INNER JOIN articulos a ON d.ArticuloID = a.ArticuloID WHERE d.FacturaID = $facturaID";
$resDetalles = $conexion->query($sqlDetalles);

// Estructura HTML para impresión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?php echo $factura['NumeroFactura']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff; color: #111; font-size: 11px; }
        .ticket-box {
            width: 7cm;
            max-width: 100vw;
            margin: 0 auto;
            background: #fff;
            padding: 6px 4px 4px 4px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .ticket-logo {
            width: 100px; height: 100px; margin: 0 auto 12px auto; display: flex; align-items: center; justify-content: center; background: #eee; border-radius: 8px;
        }
        .ticket-direccion {
            text-align: center; font-size: 0.98em; margin-bottom: 18px; color: #111;
        }
        .ticket-info {
            margin-bottom: 18px;
            font-size: 0.98em;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .ticket-info .label { font-weight: bold; display: inline-block; min-width: 70px; }
        .ticket-detalles {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 0.70em;
        }
        .ticket-detalles th, .ticket-detalles td {
            border: none;
            padding: 4px 6px;
            text-align: left;
            white-space: nowrap;
        }
        .ticket-detalles td.producto-nombre {
            white-space: normal;
            word-break: break-word;
        }
        .ticket-detalles th {
            background: #fff;
            color: #111;
            font-size: 0.98em;
            font-weight: bold;
            border-bottom: 1.5px dashed #111;
        }
        .ticket-detalles tbody tr {
            border-bottom: none;
        }
        .ticket-total, .ticket-impuesto, .ticket-efectivo, .ticket-cambio {
            font-size: 1em;
            font-weight: normal;
            color: #222;
            text-align: right;
            margin-top: 6px;
            margin-bottom: 6px;
            white-space: nowrap;
        }
        .ticket-total {
            font-weight: bold;
            color: #111;
        }
        .ticket-gracias {
            text-align: center;
            font-size: 1em;
            margin-top: 18px;
            font-weight: bold;
        }
        @media print {
            .print-btn, .no-print { display: none !important; }
            /* No modificar tamaño, fondo, bordes ni márgenes para que se imprima igual que la previsualización */
        }
    </style>
</head>
<body>
<div class="ticket-box" id="factura">
    <div class="ticket-logo">
    <img src="../images/Adigital1.jpg" alt="Logo" style="width:100px;height:100px;object-fit:contain;display:block;margin:0 auto;" onerror="this.style.display='none'" />
    </div>
    <div class="ticket-direccion">
        <!-- Cambia aquí por la dirección real de tu negocio -->
        
        <div>BO. Abajo, Ave La Libertad 1 cuandra abajo de Banco Atlantida, S. B Honduras, C. A</div>
        <div>Tel: 9714-1775  9711-3311</div>
        <div>Mail: correoNegocio@gmail.com</div>
        
    </div>
    <div class="ticket-info">
        <div><span class="label">Factura:</span> <?php echo $factura['NumeroFactura']; ?></div>
        <div><span class="label">Fecha:</span> <?php echo date('d/m/Y', strtotime($factura['FechaEmision'])); ?></div>
        <div><span class="label">Hora:</span> <?php echo date('H:i', strtotime($factura['FechaEmision'])); ?></div>
        <div><span class="label">Pago:</span> <?php echo $factura['MetodoPago']; ?></div>
    </div>
    <table class="ticket-detalles">
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Producto</th>
                <th>P.Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $totalProductos = 0; 
        $totalExento = 0;
        $totalGravado15 = 0;
        $totalISV15 = 0;
        $TASA_IVA_15 = 0.15;
        $subtotal = 0;
        mysqli_data_seek($resDetalles, 0);
        while($detalle = $resDetalles->fetch_assoc()): 
            $totalProductos += $detalle['Cantidad']; 
            $linea = round($detalle['Cantidad'] * $detalle['PrecioUnitario'], 2);
            if (isset($detalle['es_gravado']) && $detalle['es_gravado'] == 1) {
                $impuesto = round($linea * $TASA_IVA_15, 2);
                $totalGravado15 += $linea;
                $totalISV15 += $impuesto;
            } else {
                $impuesto = 0;
                $totalExento += $linea;
            }
            $subtotal += $linea;
        ?>
            <tr>
                <td>
                    <?php echo intval($detalle['Cantidad']); ?>
                    <?php
                        if (isset($detalle['es_gravado'])) {
                            echo $detalle['es_gravado'] ? ' <b>G</b>' : ' <b>E</b>';
                        }
                    ?>
                </td>
                <td class="producto-nombre">
                    <?php echo htmlspecialchars($detalle['nombre']); ?>
                </td>
                <td><?php echo 'L '.number_format($detalle['PrecioUnitario'],2); ?></td>
                <td><?php echo 'L '.number_format($linea,2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="ticket-impuesto">Sub Total: <span style="float:right">L. <?php echo number_format($subtotal,2); ?></span></div>
    <div class="ticket-impuesto">Monto Exento: <span style="float:right">L. <?php echo number_format($totalExento,2); ?></span></div>
    <div class="ticket-impuesto"><b>Gravado 15%</b>: <span style="float:right">L. <?php echo number_format($totalGravado15,2); ?></span></div>
    <div class="ticket-impuesto">I.S.V. 15%: <span style="float:right">L. <?php echo number_format($totalISV15,2); ?></span></div>
    <?php $totalPagar = round($totalExento + $totalGravado15, 2); ?>
    <div class="ticket-total">Total a pagar: <span style="float:right">L. <?php echo number_format($totalPagar,2); ?></span></div>
    <!-- Desc. Y Rebajas eliminado -->
    <div class="ticket-efectivo">Efectivo: <span style="float:right">L. <?php echo intval(round($factura['MontoRecibido'])); ?>.00</span></div>
    <?php 
        $cambio = intval(round($factura['MontoRecibido'])) - $totalPagar;
        if ($cambio < 0) $cambio = 0;
    ?>
    <div class="ticket-cambio">Cambio: <span style="float:right">L. <?php echo $cambio; ?>.00</span></div>
    <div class="ticket-gracias">¡Gracias Por Elegirnos!</div>
</div>
<script>
function imprimirFactura() {
    window.print();
}
</script>
<button class="print-btn no-print btn btn-primary nav_button" style="background: #4a90e2; color: #fff; margin: 18px auto 0 auto; display: block; width: 180px; font-size: 1.1em;" onclick="imprimirFactura()">Imprimir</button>
</body>
</html>
