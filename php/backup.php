<?php
// backup.php: Exporta la base de datos y guarda el archivo en /backups con fecha y hora
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'negocio'; // Cambia si tu base tiene otro nombre

// Contraseña para autorizar el backup (modifica aquí la clave segura)
$clave_backup = '060615';
$clave_usuario = $_POST['clave'] ?? '';
if ($clave_usuario !== $clave_backup) {
    echo json_encode(['ok' => false, 'error' => 'Contraseña incorrecta para realizar el backup.']);
    exit;
}

$fecha = date('Ymd_His');
$backup_dir = __DIR__ . '/../backups/';
$backup_file = $backup_dir . "backup_{$database}_{$fecha}.sql";

// Asegura que el directorio existe
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

$mysqldump = 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe';
$comando = "\"$mysqldump\" --user={$user} --password={$password} --host={$host} {$database} > \"{$backup_file}\" 2>&1";

$output = [];
$return_var = null;
exec($comando, $output, $return_var);

if ($return_var === 0 && filesize($backup_file) > 0) {
    echo json_encode(['ok' => true, 'file' => basename($backup_file)]);
} else {
    $errorMsg = !empty($output) ? implode("\n", $output) : 'Error al crear el backup. Verifica credenciales, permisos o ruta de mysqldump.';
    echo json_encode(['ok' => false, 'error' => $errorMsg]);
}
