<?php
include 'conexion.php';
header('Content-Type: application/json');
$res = $conexion->query("SELECT MetodoPagoID, Descripcion FROM metodopago ORDER BY MetodoPagoID");
$metodos = [];
while ($row = $res->fetch_assoc()) {
    $metodos[] = $row;
}
echo json_encode($metodos);
