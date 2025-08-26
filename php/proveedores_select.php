<?php
include 'conexion.php';
header('Content-Type: application/json');
$res = $conexion->query("SELECT ProveedorID, NombreProveedor FROM proveedores ORDER BY NombreProveedor");
$proveedores = [];
while ($row = $res->fetch_assoc()) {
    $proveedores[] = $row;
}
echo json_encode($proveedores);
