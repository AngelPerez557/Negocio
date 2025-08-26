// JS para sección Nube (Backups)

// Nueva función: enviar backup con clave
function enviarBackupConClave() {
    var clave = $('#inputClaveBackup').val();
    if (!clave) {
        $('#msgClaveBackup').text('Debes ingresar la contraseña.').show();
        return;
    }
    $('#msgClaveBackup').hide();
    $('#modalClaveBackup').modal('hide');
    $.post('php/backup.php', {clave: clave}, function(resp) {
        if (resp.ok) {
            alert('Backup creado: ' + resp.file);
        } else {
            alert(resp.error || 'Error al crear backup');
        }
    }, 'json').fail(function() {
        alert('Error de conexión al crear backup.');
    });
    $('#inputClaveBackup').val('');
}

function cargarListaBackups() {
    $.get('php/listar_backups.php', function(resp) {
        if (Array.isArray(resp)) {
            let html = '<ul class="list-group">';
            resp.forEach(function(file) {
                html += '<li class="list-group-item"><a href="backups/' + file + '" download>' + file + '</a></li>';
            });
            html += '</ul>';
            $('#lista-backups').html(html);
        } else {
            $('#lista-backups').html('<span class="text-muted">No hay backups disponibles.</span>');
        }
    }, 'json');
}

