<?php
// listar_backups.php: Devuelve la lista de archivos de backup en formato JSON
$backup_dir = __DIR__ . '/../backups/';
$archivos = [];
if (is_dir($backup_dir)) {
    foreach (scandir($backup_dir) as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $archivos[] = $file;
        }
    }
}
echo json_encode($archivos);
