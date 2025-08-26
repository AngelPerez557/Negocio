<?php
include 'conexion.php';
$date = $_GET['fecha'] ?? date('Y-m-d');
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro de Ventas Diario</title>
    <link rel="stylesheet" href="../css/bootstrap-3.4.1-dist/css/bootstrap.min.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #fff; color: #111; font-size: 13px; }
        .reporte-box { max-width: 1100px; margin: 30px auto; background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        h2 { text-align: center; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 6px 8px; text-align: center; font-size: 13px; }
        th { background: #f5f5f5; }
        .totales { font-weight: bold; background: #e9ecef; }
        .form-inline { text-align: center; margin-bottom: 18px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
<div class="reporte-box">
    <h2>Libro de Ventas Diario</h2>
    <form class="form-inline no-print" method="get">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($date); ?>" class="form-control" required>
        <button type="submit" class="btn btn-primary">Ver Reporte</button>
        <button type="button" class="btn btn-default" onclick="window.print()">Imprimir</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th># Factura</th>
                <th>Cliente</th>
                <th>RTN/Identidad</th>
                <th>Gravado</th>
                <th>Exento</th>
                <th>ISV</th>
                <th>Total</th>
                <th>Método Pago</th>
            </tr>
        </thead>
        <tbody>
<?php
$sql = "SELECT f.FechaEmision, f.NumeroFactura, f.TotalFactura, f.FacturaID, m.Descripcion as MetodoPago
        FROM facturas f
        LEFT JOIN metodopago m ON f.MetodoPagoID = m.MetodoPagoID
        WHERE DATE(f.FechaEmision) = ?
        ORDER BY f.FechaEmision ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $date);
$stmt->execute();
$res = $stmt->get_result();

$total_gravado = $total_exento = $total_isv = $total_total = 0;
while ($row = $res->fetch_assoc()) {
    // Calcular gravado, exento e ISV por factura
    $sql_det = "SELECT d.Cantidad, d.PrecioUnitario, a.es_gravado FROM facturadetalle d INNER JOIN articulos a ON d.ArticuloID = a.ArticuloID WHERE d.FacturaID = ?";
    $stmt_det = $conexion->prepare($sql_det);
    $stmt_det->bind_param('i', $row['FacturaID']);
    $stmt_det->execute();
    $res_det = $stmt_det->get_result();
    $gravado = $exento = 0;
    while ($det = $res_det->fetch_assoc()) {
        if ($det['es_gravado']) {
            $gravado += $det['Cantidad'] * $det['PrecioUnitario'];
        } else {
            $exento += $det['Cantidad'] * $det['PrecioUnitario'];
        }
    }
    $isv = round($gravado * 0.15, 2); // 15% ISV
    $total = $gravado + $exento + $isv;
    $total_gravado += $gravado;
    $total_exento += $exento;
    $total_isv += $isv;
    $total_total += $total;
    echo '<tr>';
    echo '<td>' . htmlspecialchars(substr($row['FechaEmision'],0,10)) . '</td>';
    echo '<td>' . htmlspecialchars($row['NumeroFactura']) . '</td>';
    echo '<td>Consumidor Final</td>';
    echo '<td>-</td>';
    echo '<td>' . number_format($gravado,2) . '</td>';
    echo '<td>' . number_format($exento,2) . '</td>';
    echo '<td>' . number_format($isv,2) . '</td>';
    echo '<td>' . number_format($total,2) . '</td>';
    echo '<td>' . htmlspecialchars($row['MetodoPago']) . '</td>';
    echo '</tr>';
}
?>
        </tbody>
        <tfoot>
            <tr class="totales">
                <td colspan="4">Totales</td>
                <td><?php echo number_format($total_gravado,2); ?></td>
                <td><?php echo number_format($total_exento,2); ?></td>
                <td><?php echo number_format($total_isv,2); ?></td>
                <td><?php echo number_format($total_total,2); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div class="no-print" style="text-align:center;color:#888;font-size:12px;">Reporte generado el <?php echo date('d/m/Y H:i'); ?></div>
</div>
</body>
</html>
