<?php
// reportes_ventas.php: Devuelve totales de ventas diario, semanal, mensual y anual
include 'conexion.php';
header('Content-Type: application/json');

// Total vendido hoy
$sql_hoy = "SELECT IFNULL(SUM(TotalFactura),0) AS total_hoy FROM facturas WHERE DATE(FechaEmision) = CURDATE()";
$res_hoy = $conexion->query($sql_hoy);
$total_hoy = $res_hoy ? $res_hoy->fetch_assoc()['total_hoy'] : 0;

// Total vendido en los últimos 7 días (incluyendo hoy), comparando solo la fecha
$sql_semana = "SELECT IFNULL(SUM(TotalFactura),0) AS total_semana FROM facturas WHERE DATE(FechaEmision) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND DATE(FechaEmision) <= CURDATE()";
$res_semana = $conexion->query($sql_semana);
$total_semana = $res_semana ? $res_semana->fetch_assoc()['total_semana'] : 0;

// Total vendido mes actual (comparando solo la fecha)
$sql_mes = "SELECT IFNULL(SUM(TotalFactura),0) AS total_mes FROM facturas WHERE YEAR(DATE(FechaEmision)) = YEAR(CURDATE()) AND MONTH(DATE(FechaEmision)) = MONTH(CURDATE())";
$res_mes = $conexion->query($sql_mes);
$total_mes = $res_mes ? $res_mes->fetch_assoc()['total_mes'] : 0;

// Total vendido año actual (comparando solo la fecha)
$sql_anio = "SELECT IFNULL(SUM(TotalFactura),0) AS total_anio FROM facturas WHERE YEAR(DATE(FechaEmision)) = YEAR(CURDATE())";
$res_anio = $conexion->query($sql_anio);
$total_anio = $res_anio ? $res_anio->fetch_assoc()['total_anio'] : 0;


// Función para obtener ventas gravadas y exentas en un rango de fechas
function obtenerVentasGravadasExentas($conexion, $whereFecha) {
    $sql = "SELECT 
        SUM(CASE WHEN a.es_gravado = 1 THEN d.Cantidad * d.PrecioUnitario ELSE 0 END) AS total_gravado,
        SUM(CASE WHEN a.es_gravado = 0 THEN d.Cantidad * d.PrecioUnitario ELSE 0 END) AS total_exento
        FROM facturadetalle d
        INNER JOIN facturas f ON d.FacturaID = f.FacturaID
        INNER JOIN articulos a ON d.ArticuloID = a.ArticuloID
        WHERE $whereFecha";
    $res = $conexion->query($sql);
    $row = $res ? $res->fetch_assoc() : ['total_gravado'=>0,'total_exento'=>0];
    return [
        'gravado' => floatval($row['total_gravado']),
        'exento' => floatval($row['total_exento'])
    ];
}

$ventas_hoy = obtenerVentasGravadasExentas($conexion, "DATE(f.FechaEmision) = CURDATE()");
$ventas_semana = obtenerVentasGravadasExentas($conexion, "DATE(f.FechaEmision) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND DATE(f.FechaEmision) <= CURDATE()");
$ventas_mes = obtenerVentasGravadasExentas($conexion, "YEAR(DATE(f.FechaEmision)) = YEAR(CURDATE()) AND MONTH(DATE(f.FechaEmision)) = MONTH(CURDATE())");
$ventas_anio = obtenerVentasGravadasExentas($conexion, "YEAR(DATE(f.FechaEmision)) = YEAR(CURDATE())");

$respuesta = [
    'total_hoy' => $total_hoy,
    'total_semana' => $total_semana,
    'total_mes' => $total_mes,
    'total_anio' => $total_anio,
    'gravado_hoy' => $ventas_hoy['gravado'],
    'exento_hoy' => $ventas_hoy['exento'],
    'gravado_semana' => $ventas_semana['gravado'],
    'exento_semana' => $ventas_semana['exento'],
    'gravado_mes' => $ventas_mes['gravado'],
    'exento_mes' => $ventas_mes['exento'],
    'gravado_anio' => $ventas_anio['gravado'],
    'exento_anio' => $ventas_anio['exento']
];
echo json_encode($respuesta);
