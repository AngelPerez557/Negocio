    // Filtro en tiempo real para la tabla de proveedores
    $(document).on('input', '#buscador-proveedores', function() {
        var filtro = $(this).val().toLowerCase().replace(/\s+/g, '');
        $('#tabla-proveedores tbody tr').each(function() {
            var nombre = $(this).find('td').eq(0).text().toLowerCase().replace(/\s+/g, '');
            var contacto = $(this).find('td').eq(1).text().toLowerCase().replace(/\s+/g, '');
            var telefono = $(this).find('td').eq(2).text().toLowerCase().replace(/\s+/g, '');
            var direccion = $(this).find('td').eq(3).text().toLowerCase().replace(/\s+/g, '');
            if (
                nombre.includes(filtro) ||
                contacto.includes(filtro) ||
                telefono.includes(filtro) ||
                direccion.includes(filtro)
            ) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
$(document).ready(function() {
    // --- PROVEEDORES CRUD ---
    function cargarProveedores() {
        $.get('php/proveedores_api.php', {accion: 'listar'}, function(data) {
            var tbody = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function(p) {
                    tbody += `<tr>
                        <td>${p.NombreProveedor}</td>
                        <td>${p.Contacto}</td>
                        <td>${p.Telefono}</td>
                        <td>${p.Direccion}</td>
                        <td>
                            <button class='btn btn-xs btn-info' onclick='abrirModalProveedor(${p.ProveedorID}, "${p.NombreProveedor}", "${p.Contacto}", "${p.Telefono}", "${p.Direccion}")'><i class='fa fa-edit'></i></button>
                            <button class='btn btn-xs btn-danger' onclick='eliminarProveedor(${p.ProveedorID})'><i class='fa fa-trash'></i></button>
                        </td>
                    </tr>`;
                });
            } else {
                tbody = '<tr><td colspan="5" class="text-center">No hay proveedores registrados.</td></tr>';
            }
            $('#tabla-proveedores tbody').html(tbody);
        }, 'json');
    }

    window.abrirModalProveedor = function(id, nombre, contacto, telefono, direccion) {
        if (id) {
            $('#modalProveedorLabel').text('Editar Proveedor');
            $('#proveedor-id').val(id);
            $('#proveedor-nombre').val(nombre);
            $('#proveedor-contacto').val(contacto);
            $('#proveedor-telefono').val(telefono);
            $('#proveedor-direccion').val(direccion);
        } else {
            $('#modalProveedorLabel').text('Agregar Proveedor');
            $('#formProveedor')[0].reset();
            $('#proveedor-id').val('');
        }
        $('#modalProveedor').modal('show');
    };

    window.guardarProveedor = function() {
        var id = $('#proveedor-id').val();
        var nombre = $('#proveedor-nombre').val();
        var contacto = $('#proveedor-contacto').val();
        var telefono = $('#proveedor-telefono').val();
        var direccion = $('#proveedor-direccion').val();
        var accion = id ? 'editar' : 'agregar';
        $.post('php/proveedores_api.php', {
            accion: accion,
            id: id,
            nombre: nombre,
            contacto: contacto,
            telefono: telefono,
            direccion: direccion
        }, function(resp) {
            $('#modalProveedor').modal('hide');
            cargarProveedores();
        }, 'json').fail(function(xhr) {
            alert('Error al guardar proveedor.');
        });
    };

    window.eliminarProveedor = function(id) {
        if (!confirm('¿Seguro que deseas eliminar este proveedor?')) return;
        $.post('php/proveedores_api.php', {accion: 'eliminar', id: id}, function(resp) {
            cargarProveedores();
        }, 'json').fail(function(xhr) {
            alert('Error al eliminar proveedor.');
        });
    };

    // Cargar proveedores solo cuando se muestra la subsección de proveedores al hacer clic en los botones de navegación
    $(document).on('click', '.nav_button', function() {
        if ($('#Inventario_Proveedores').is(':visible')) {
            cargarProveedores();
        }
    });
    // Mostrar la sección principal de Inventario y subsección Artículos al cargar
    function mostrarInventarioPorDefecto() {
        $("#Inventario").removeClass("hidden");
        $("#Inventario .subseccion").addClass("hidden");
        $("#Inventario_Articulos").removeClass("hidden");
    }
    mostrarInventarioPorDefecto();

    // Función para mostrar secciones principales
    window.mostrarSeccion = function(id) {
        $(".seccion-principal").addClass("hidden");
        $("#" + id).removeClass("hidden");
        // Si es Inventario, mostrar subsección por defecto
        if (id === 'Inventario') {
            $("#Inventario .subseccion").addClass("hidden");
            $("#Inventario_Articulos").removeClass("hidden");
        }
        // Si es Nube, cargar backups
        if (id === 'Nube' && typeof cargarListaBackups === 'function') {
            cargarListaBackups();
        }
    };

    // Función para mostrar subsecciones dentro de Inventario
    window.mostrarSubseccion = function(seccion, sub) {
        if (!seccion || !sub) return;
        $("#" + seccion + " .subseccion").addClass("hidden");
        $("#" + sub).removeClass("hidden");
    };

    // Función para mostrar solo la subsección de proveedores
    window.mostrarProveedoresSubseccion = function() {
        window.mostrarSubseccion('Inventario', 'Inventario_Proveedores');
    };
});