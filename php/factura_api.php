<?php
include 'conexion.php';
header('Content-Type: application/json');

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'guardar':
        $metodo = intval($_POST['MetodoPagoID'] ?? 0);
        $montoRecibido = floatval($_POST['MontoRecibido'] ?? 0);
        $detalle = json_decode($_POST['detalle'] ?? '[]', true);
        if (!$metodo || $montoRecibido <= 0 || !is_array($detalle) || count($detalle) == 0) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
            exit;
        }
        // Calcular totales
        $subtotal = 0;
        $total = 0;
        foreach ($detalle as $item) {
            $subtotal += $item['PrecioUnitario'] * $item['Cantidad'];
        }
        $total = $subtotal; // Aquí puedes sumar impuestos si lo deseas
        // Insertar factura
        $stmt = $conexion->prepare("INSERT INTO facturas (NumeroFactura, FechaEmision, Subtotal, TotalFactura, MontoRecibido, MetodoPagoID) VALUES (?, NOW(), ?, ?, ?, ?)");
        $numero = time(); // Puedes mejorar la lógica del número de factura
        $stmt->bind_param('sdddi', $numero, $subtotal, $total, $montoRecibido, $metodo);
        $ok = $stmt->execute();
        if (!$ok) {
            echo json_encode(['ok' => false, 'msg' => 'Error al guardar factura']);
            exit;
        }
        $facturaID = $stmt->insert_id;
        // Insertar detalle
        $stmtDetalle = $conexion->prepare("INSERT INTO facturadetalle (FacturaID, ArticuloID, Cantidad, PrecioUnitario) VALUES (?, ?, ?, ?)");
        foreach ($detalle as $item) {
            $stmtDetalle->bind_param('iiid', $facturaID, $item['ArticuloID'], $item['Cantidad'], $item['PrecioUnitario']);
            $stmtDetalle->execute();
        }
        echo json_encode(['ok' => true, 'facturaID' => $facturaID]);
        break;
    default:
        echo json_encode(['ok' => false, 'msg' => 'Acción no válida']);
}
