<?php
include 'conexion.php';
header('Content-Type: application/json');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $sql = "SELECT * FROM proveedores ORDER BY ProveedorID DESC";
        $res = $conexion->query($sql);
        $proveedores = [];
        while ($row = $res->fetch_assoc()) {
            $proveedores[] = $row;
        }
        echo json_encode($proveedores);
        break;
    case 'agregar':
        $nombre = $_POST['nombre'] ?? '';
        $contacto = $_POST['contacto'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $sql = "INSERT INTO proveedores (NombreProveedor, Contacto, Telefono, Direccion) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssss', $nombre, $contacto, $telefono, $direccion);
        $ok = $stmt->execute();
        echo json_encode(['ok' => $ok]);
        break;
    case 'editar':
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $contacto = $_POST['contacto'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $sql = "UPDATE proveedores SET NombreProveedor=?, Contacto=?, Telefono=?, Direccion=? WHERE ProveedorID=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssssi', $nombre, $contacto, $telefono, $direccion, $id);
        $ok = $stmt->execute();
        echo json_encode(['ok' => $ok]);
        break;
    case 'eliminar':
        $id = $_POST['id'] ?? 0;
        $sql = "DELETE FROM proveedores WHERE ProveedorID=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        echo json_encode(['ok' => $ok]);
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
}
