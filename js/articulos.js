    // Siempre mantener cantidad >= 1 y no vacío
    $(document).on('input', '#factura-cantidad', function() {
        var val = parseInt($(this).val());
        if (isNaN(val) || val < 1) {
            $(this).val(1);
        }
    });
    // Limpiar búsqueda de facturas
    $(document).on('click', '#limpiar-busqueda-factura', function() {
        $('#buscar-fecha').val('');
        $('#buscar-hora').val('');
        $('#tabla-buscar-facturas tbody').empty();
    });
    // --- BUSCAR FACTURAS ---
    $(document).on('submit', '#formBuscarFactura', function(e) {
        e.preventDefault();
        var fecha = $('#buscar-fecha').val();
        var hora = $('#buscar-hora').val();
        $.get('php/buscar_facturas.php', {fecha: fecha, hora: hora}, function(data) {
            var tbody = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function(f) {
                    var fechaObj = new Date(f.FechaEmision);
                    var fechaStr = fechaObj.toLocaleDateString('es-ES');
                    var horaStr = fechaObj.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                    tbody += `<tr>
                        <td>${f.NumeroFactura}</td>
                        <td>${fechaStr}</td>
                        <td>${horaStr}</td>
                        <td>L. ${parseFloat(f.TotalFactura).toFixed(2)}</td>
                        <td>${f.MetodoPago || ''}</td>
                        <td><button class='btn btn-xs btn-info' onclick='window.open("php/factura.php?id=${f.FacturaID}", "_blank")'><i class='fa fa-print'></i> Ver/Imprimir</button></td>
                    </tr>`;
                });
            } else {
                tbody = '<tr><td colspan="6" class="text-center">No se encontraron facturas.</td></tr>';
            }
            $('#tabla-buscar-facturas tbody').html(tbody);
        }, 'json');
    });
    // Guardar todos los artículos para búsqueda
    var articulosFacturaTodos = [];

    // Select2 eliminado, aquí puedes agregar lógica de búsqueda personalizada si lo deseas
    // Guardar factura
    window.guardarFacturaNueva = function() {
        var metodo = $('#factura-metodo').val();
        var monto = parseFloat($('#factura-monto').val());
        if (!metodo || !monto || facturaDetalle.length == 0) {
            alert('Completa todos los datos y agrega al menos un artículo.');
            return;
        }
        $.post('php/factura_api.php', {
            accion: 'guardar',
            MetodoPagoID: metodo,
            MontoRecibido: monto,
            detalle: JSON.stringify(facturaDetalle)
        }, function(resp) {
            if (resp.ok) {
                alert('Factura guardada correctamente.');
                window.open('php/factura.php?id=' + resp.facturaID, '_blank');
                // Limpiar formulario y detalle
                $('#formFacturaNueva')[0].reset();
                facturaDetalle = [];
                renderFacturaDetalle();
            } else {
                alert(resp.msg || 'Error al guardar la factura.');
            }
        }, 'json').fail(function() {
            alert('Error de conexión al guardar la factura.');
        });
    };
    // --- FACTURACIÓN NUEVA ---
    // Cargar métodos de pago
    function cargarMetodosPago() {
        $.get('php/metodopago_select.php', function(data) {
            var options = '<option value="">Seleccione método</option>';
            data.forEach(function(m) {
                options += `<option value="${m.MetodoPagoID}">${m.Descripcion}</option>`;
            });
            $('#factura-metodo').html(options);
        }, 'json');
    }

    // Cargar artículos para el select de factura
    function cargarArticulosFactura() {
        $.get('php/articulos_api.php', {accion: 'listar'}, function(data) {
            articulosFacturaTodos = data;
            renderOpcionesArticulosFactura('');
        }, 'json');
    }

    // Renderizar opciones del select según filtro
    function renderOpcionesArticulosFactura(filtro) {
        var options = '<option value="">Seleccione artículo</option>';
        filtro = filtro.toLowerCase();
        articulosFacturaTodos.forEach(function(a) {
            var nombre = a.nombre.toLowerCase();
            var codigo = (a.CodigoBarra || '').toString().toLowerCase();
            var tipo = (parseInt(a.es_gravado) === 1) ? 'G' : 'E';
            if (nombre.includes(filtro) || codigo.includes(filtro)) {
                options += `<option value="${a.ArticuloID}" data-precio="${a.PrecioVenta}" data-codigo="${a.CodigoBarra}">${a.nombre} [${tipo}]</option>`;
            }
        });
        $('#factura-articulo').html(options);
    }

    // Filtrar artículos al escribir
    $(document).on('input', '#buscador-articulo-factura', function() {
        var filtro = $(this).val();
        renderOpcionesArticulosFactura(filtro);
    });

    // Estado temporal de la factura
    var facturaDetalle = [];

    // Agregar artículo a la factura
    window.agregarArticuloFactura = function() {
        var articuloID = $('#factura-articulo').val();
        var cantidad = parseInt($('#factura-cantidad').val());
        if (!articuloID || !cantidad || cantidad < 1) return;
        var $opt = $('#factura-articulo option:selected');
        var nombre = $opt.text();
        var precio = parseFloat($opt.data('precio'));
        var codigo = $opt.data('codigo');
        // Si ya existe, suma cantidad
        var existe = facturaDetalle.findIndex(x => x.ArticuloID == articuloID);
        if (existe >= 0) {
            facturaDetalle[existe].Cantidad += cantidad;
        } else {
            facturaDetalle.push({ArticuloID: articuloID, nombre: nombre, CodigoBarra: codigo, PrecioUnitario: precio, Cantidad: cantidad});
        }
        renderFacturaDetalle();
    $('#factura-cantidad').val(1);
    };

    // Renderizar tabla de detalle
    function renderFacturaDetalle() {
        var tbody = '';
        var total = 0;
        facturaDetalle.forEach(function(item, idx) {
            var subtotal = item.PrecioUnitario * item.Cantidad;
            total += subtotal;
            tbody += `<tr>
                <td>${item.nombre}</td>
                <td>${item.CodigoBarra}</td>
                <td>L. ${item.PrecioUnitario.toFixed(2)}</td>
                <td>${item.Cantidad}</td>
                <td>L. ${subtotal.toFixed(2)}</td>
                <td><button class='btn btn-xs btn-danger' onclick='eliminarArticuloFactura(${idx})'><i class='fa fa-trash'></i></button></td>
            </tr>`;
        });
        $('#tabla-factura-detalle tbody').html(tbody);
        $('#factura-total').text('L. ' + total.toFixed(2));
    }

    // Eliminar artículo del detalle
    window.eliminarArticuloFactura = function(idx) {
        facturaDetalle.splice(idx, 1);
        renderFacturaDetalle();
    };

    // Inicializar selects al mostrar sección de factura nueva
    $(document).on('click', "button.nav_button, .nav_button", function() {
        if ($('#Factura_Hacer').is(':visible')) {
            cargarMetodosPago();
            cargarArticulosFactura();
            facturaDetalle = [];
            renderFacturaDetalle();
        }
    });
    // Filtro en tiempo real para la tabla de artículos (nombre o código de barra)
    $(document).on('input', '#buscador-articulos', function() {
        var filtro = $(this).val().toLowerCase().replace(/\s+/g, '');
        $('#tabla-articulos tbody tr').each(function() {
            var nombre = $(this).find('td').eq(0).text().toLowerCase().replace(/\s+/g, '');
            var codigo = $(this).find('td').eq(1).text().toLowerCase().replace(/\s+/g, '');
            if (nombre.includes(filtro) || codigo.includes(filtro)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
$(document).ready(function() {
    // Cargar proveedores en el select del modal
    function cargarProveedoresSelect(selectedId) {
        $.get('php/proveedores_select.php', function(data) {
            var options = '<option value="">Seleccione proveedor</option>';
            data.forEach(function(p) {
                options += `<option value="${p.ProveedorID}" ${selectedId == p.ProveedorID ? 'selected' : ''}>${p.NombreProveedor}</option>`;
            });
            var $select = $('#articulo-proveedor');
            $select.html(options);
            // Select2 eliminado, aquí puedes agregar lógica de búsqueda personalizada si lo deseas
        }, 'json');
    }
    function cargarArticulos() {
        $.get('php/articulos_api.php', {accion: 'listar'}, function(data) {
            var tbody = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function(a) {
                    tbody += `<tr>
                        <td>${a.nombre}</td>
                        <td>${a.CodigoBarra}</td>
                        <td>${a.stock}</td>
                        <td>${a.PrecioVenta}</td>
                        <td>${a.PrecioCosto}</td>
                        <td>${a.NombreProveedor ? a.NombreProveedor : ''}</td>
                            <td>${parseInt(a.es_gravado) === 1 ? 'Gravado' : 'Exento'}</td>
                        <td>
                            <button class='btn btn-xs btn-info' onclick='abrirModalArticulo(${a.ArticuloID}, "${a.nombre}", "${a.CodigoBarra}", ${a.stock}, ${a.PrecioVenta}, ${a.PrecioCosto}, ${a.ProveedorID}, ${a.es_gravado})'><i class='fa fa-edit'></i></button>
                            <button class='btn btn-xs btn-danger' onclick='eliminarArticulo(${a.ArticuloID})'><i class='fa fa-trash'></i></button>
                        </td>
                    </tr>`;
                });
            } else {
                tbody = '<tr><td colspan="9" class="text-center">No hay artículos registrados.</td></tr>';
            }
            $('#tabla-articulos tbody').html(tbody);
        }, 'json');
    }

    window.abrirModalArticulo = function(id, nombre, codigo, stock, precioVenta, precioCosto, proveedor, es_gravado) {
        if (id) {
            $('#modalArticuloLabel').text('Editar Artículo');
            $('#articulo-id').val(id);
            $('#articulo-nombre').val(nombre);
            $('#articulo-codigo').val(codigo);
            $('#articulo-stock').val(stock);
            $('#articulo-precioventa').val(precioVenta);
            $('#articulo-preciocosto').val(precioCosto);
            cargarProveedoresSelect(proveedor);
            $('#articulo-esgravado').val(es_gravado);
        } else {
            $('#modalArticuloLabel').text('Agregar Artículo');
            $('#formArticulo')[0].reset();
            $('#articulo-id').val('');
            cargarProveedoresSelect();
        }
    // Select2 eliminado, no es necesario abrir el select
        $('#modalArticulo').modal('show');
    };

    window.guardarArticulo = function() {
        var id = $('#articulo-id').val();
        var nombre = $('#articulo-nombre').val().trim();
        var codigo = $('#articulo-codigo').val().trim();
        var stock = $('#articulo-stock').val();
        var precioVenta = $('#articulo-precioventa').val();
        var precioCosto = $('#articulo-preciocosto').val();
        var proveedor = $('#articulo-proveedor').val();
        var es_gravado = $('#articulo-esgravado').val();
        var accion = id ? 'editar' : 'agregar';
        // Validación frontend
        if (!nombre || !codigo || stock === '' || precioVenta === '' || precioCosto === '' || proveedor === null || proveedor === undefined || proveedor === '') {
            alert('Completa todos los campos obligatorios.');
            return;
        }
        if (isNaN(stock) || isNaN(precioVenta) || isNaN(precioCosto)) {
            alert('Stock, Precio Venta y Precio Costo deben ser números.');
            return;
        }
        // Si proveedor es vacío, poner 0
        if (!proveedor) proveedor = 0;
        $.post('php/articulos_api.php', {
            accion: accion,
            id: id,
            nombre: nombre,
            CodigoBarra: codigo,
            stock: stock,
            PrecioVenta: precioVenta,
            PrecioCosto: precioCosto,
            ProveedorID: proveedor,
            es_gravado: es_gravado
        }, function(resp) {
            if (resp.ok) {
                $('#modalArticulo').modal('hide');
                cargarArticulos();
            } else {
                alert(resp.error || 'Error al guardar artículo.');
            }
        }, 'json').fail(function(xhr) {
            let msg = 'Error al guardar artículo.';
            if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            alert(msg);
        });
    };

    window.eliminarArticulo = function(id) {
        if (!confirm('¿Seguro que deseas eliminar este artículo?')) return;
        $.post('php/articulos_api.php', {accion: 'eliminar', id: id}, function(resp) {
            cargarArticulos();
        }, 'json').fail(function(xhr) {
            alert('Error al eliminar artículo.');
        });
    };

    // Cargar artículos al hacer clic en la pestaña de artículos
    $(document).on('click', '.nav_button', function() {
        if ($('#Inventario_Articulos').is(':visible')) {
            cargarArticulos();
        }
    });
});
