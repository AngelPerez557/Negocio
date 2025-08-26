<?php
include 'php/conexion.php';

if ($conexion->connect_error) {
    echo 'Error de conexión: ' . $conexion->connect_error;
} else {
    echo 'Conexión exitosa';
}
?>
