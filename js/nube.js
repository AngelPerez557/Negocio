// Reportes de ventas
function cargarReportesVentas() {
    $.get('php/reportes_ventas.php', function(resp) {
        if (resp && typeof resp === 'object') {
            $('#reporte-hoy').text('L. ' + parseFloat(resp.total_hoy).toFixed(2));
            $('#reporte-semana').text('L. ' + parseFloat(resp.total_semana).toFixed(2));
            $('#reporte-mes').text('L. ' + parseFloat(resp.total_mes).toFixed(2));
            $('#reporte-anio').text('L. ' + parseFloat(resp.total_anio).toFixed(2));
            // Mostrar gravado y exento
            $('#reporte-hoy-gravado').text('L. ' + parseFloat(resp.gravado_hoy).toFixed(2));
            $('#reporte-hoy-exento').text('L. ' + parseFloat(resp.exento_hoy).toFixed(2));
            $('#reporte-semana-gravado').text('L. ' + parseFloat(resp.gravado_semana).toFixed(2));
            $('#reporte-semana-exento').text('L. ' + parseFloat(resp.exento_semana).toFixed(2));
            $('#reporte-mes-gravado').text('L. ' + parseFloat(resp.gravado_mes).toFixed(2));
            $('#reporte-mes-exento').text('L. ' + parseFloat(resp.exento_mes).toFixed(2));
            $('#reporte-anio-gravado').text('L. ' + parseFloat(resp.gravado_anio).toFixed(2));
            $('#reporte-anio-exento').text('L. ' + parseFloat(resp.exento_anio).toFixed(2));
        }
    }, 'json');
}

// Cargar reportes al mostrar la subsección de reportes
$(document).ready(function() {
    // Cargar al entrar a Inventario
    if ($('#Inventario').is(':visible')) cargarReportesVentas();

    // Cargar al hacer clic en el botón de Reportes
    $(document).on('click', '.nav_button', function() {
        var seccion = $(this).attr('onclick');
        if (seccion && seccion.includes("Inventario_Reportes")) {
            setTimeout(cargarReportesVentas, 200);
        }
    });

    // Cargar al mostrar la subsección de reportes por cualquier otro método
    $(document).on('show', '#Inventario_Reportes', function() {
        cargarReportesVentas();
    });
});
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

