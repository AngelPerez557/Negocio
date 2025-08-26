<?php
include 'conexion.php';
header('Content-Type: application/json');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $sql = "SELECT a.*, p.NombreProveedor FROM articulos a LEFT JOIN proveedores p ON a.ProveedorID = p.ProveedorID ORDER BY a.ArticuloID DESC";
        $res = $conexion->query($sql);
        $articulos = [];
        while ($row = $res->fetch_assoc()) {
            $articulos[] = $row;
        }
        echo json_encode($articulos);
        break;
    case 'agregar':
        $nombre = $_POST['nombre'] ?? '';
        $codigo = $_POST['CodigoBarra'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        $precioVenta = $_POST['PrecioVenta'] ?? 0;
        $precioCosto = $_POST['PrecioCosto'] ?? 0;
        $proveedor = $_POST['ProveedorID'] ?? 0;
        $es_gravado = $_POST['es_gravado'] ?? 0;
        $sql = "INSERT INTO articulos (nombre, CodigoBarra, stock, PrecioVenta, PrecioCosto, ProveedorID, es_gravado) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssiddii', $nombre, $codigo, $stock, $precioVenta, $precioCosto, $proveedor, $es_gravado);
        $ok = $stmt->execute();
        if (!$ok) {
            echo json_encode(['ok' => false, 'error' => $stmt->error]);
        } else {
            echo json_encode(['ok' => true]);
        }
        break;
    case 'editar':
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $codigo = $_POST['CodigoBarra'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        $precioVenta = $_POST['PrecioVenta'] ?? 0;
        $precioCosto = $_POST['PrecioCosto'] ?? 0;
        $proveedor = $_POST['ProveedorID'] ?? 0;
        $es_gravado = $_POST['es_gravado'] ?? 0;
        $sql = "UPDATE articulos SET nombre=?, CodigoBarra=?, stock=?, PrecioVenta=?, PrecioCosto=?, ProveedorID=?, es_gravado=? WHERE ArticuloID=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssiddiii', $nombre, $codigo, $stock, $precioVenta, $precioCosto, $proveedor, $es_gravado, $id);
        $ok = $stmt->execute();
        echo json_encode(['ok' => $ok]);
        break;
    case 'eliminar':
        $id = $_POST['id'] ?? 0;
        $sql = "DELETE FROM articulos WHERE ArticuloID=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        echo json_encode(['ok' => $ok]);
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
}
