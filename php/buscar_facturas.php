<?php
include 'conexion.php';
header('Content-Type: application/json');

$fecha = $_GET['fecha'] ?? '';
$hora = $_GET['hora'] ?? '';
$where = [];
$params = [];
$types = '';

if ($fecha) {
    $where[] = 'DATE(FechaEmision) = ?';
    $params[] = $fecha;
    $types .= 's';
}
if ($hora) {
    // Buscar facturas dentro de la hora seleccionada (ej: 14:00:00 a 14:59:59)
    $where[] = '(HOUR(FechaEmision) = ?)';
    $params[] = intval(explode(':', $hora)[0]);
    $types .= 'i';
}
$sql = "SELECT f.FacturaID, f.NumeroFactura, f.FechaEmision, f.TotalFactura, m.Descripcion as MetodoPago FROM facturas f LEFT JOIN metodopago m ON f.MetodoPagoID = m.MetodoPagoID";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY f.FacturaID DESC';
$stmt = $conexion->prepare($sql);
if ($where) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$facturas = [];
while ($row = $res->fetch_assoc()) {
    $facturas[] = $row;
}
echo json_encode($facturas);
