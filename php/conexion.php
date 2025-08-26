<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'negocio'; // Cambia esto por el nombre real de tu base de datos

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
	die('Error de conexión: ' . $conexion->connect_error);
}
// echo 'Conexión exitosa'; // Descomenta para probar la conexión
?>
