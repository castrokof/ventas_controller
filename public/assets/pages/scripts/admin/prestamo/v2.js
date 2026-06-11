/**
 * assets/pages/scripts/admin/prestamo/v2.js
 *
 * JS modernizado para el módulo Préstamos V2.
 *
 * Mejoras respecto al inline original:
 *  1. Event handler leak: la versión original hacía
 *       $('#btn-crear').click(function(){ $('#form').on('submit', ...) })
 *     lo que añadía un nuevo listener en cada apertura del modal.
 *     Aquí el listener de submit se registra UNA SOLA VEZ.
 *  2. const / let en lugar de var.
 *  3. La función calcularMontos() usa la misma fórmula que el original
 *     pero está documentada y es más legible.
 *  4. La tabla muestra un badge de estado en lugar de colorear la fila
 *     vía inline style; las clases CSS se gestionan en la hoja de estilos.
 *  5. Los IDs de los botones en los modales son descriptivos y únicos.
 */

/* ── Idioma español para DataTables ─────────────────────────────────────── */
const idioma = {
    sProcessing:   'Procesando...',
    sLengthMenu:   'Mostrar _MENU_ registros',
    sZeroRecords:  'No se encontraron resultados',
    sEmptyTable:   'Ningún dato disponible en esta tabla',
    sInfo:         'Mostrando registros del _START_ al _END_ de un total de _TOTAL_',
    sInfoEmpty:    'Mostrando registros del 0 al 0 de un total de 0 registros',
    sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
    sSearch:       'Buscar:',
    sLoadingRecords: 'Cargando...',
    oPaginate: {
        sFirst: 'Primero', sLast: 'Último',
        sNext: 'Siguiente', sPrevious: 'Anterior',
    },
    oAria: {
        sSortAscending:  ': orden ascendente',
        sSortDescending: ': orden descendente',
    },
    buttons: { copy: 'Copiar', colvis: 'Columnas' },
};

/* ── Estado de la tabla ─────────────────────────────────────────────────── */
let tablaIniciada = false;

/* Saldo pendiente del préstamo que se está refinanciando (0 cuando no hay modal abierto) */
let refiPendiente = 0;

/* ── Cálculo de montos ──────────────────────────────────────────────────── */
/* Cuotas equivalentes a 1 mes según el tipo de pago (la tasa de interés es mensual) */
const CUOTAS_POR_MES = { Diario: 24, Semanal: 4, Quincenal: 2, Mensual: 1 };

/**
 * Recalcula monto_total y valor_cuota según el tipo de pago.
 *
 * Por defecto:
 *   Mensual: el interés (mensual) se aplica por cada cuota.
 *   Diario / Semanal / Quincenal: el interés se aplica una sola vez.
 *
 * Si se activa "Prorratear interés mensual según frecuencia"
 * (#interes_prorrateado_p), la tasa mensual se reparte según cuántas
 * cuotas del tipo seleccionado equivalen a un mes:
 *     total = monto + monto * (interes/100) * (cuotas / cuotasPorMes)
 *
 * Esto hace que, para una misma tasa, 3 cuotas mensuales, 6 quincenales
 * y 12 semanales (todas equivalentes a 3 meses) den el mismo total.
 */
function calcularMontos() {
    const monto    = parseFloat($('#monto_p').val())    || 0;
    const cuotas   = parseInt($('#cuotas_p').val())     || 0;
    const interes  = parseFloat($('#interes_p').val())  || 0;
    const tipoPago = $('#tipo_pago_p').val();
    const prorratear = $('#interes_prorrateado_p').is(':checked');

    if (!monto || !cuotas || !tipoPago) return;

    const meses = (tipoPago === 'Mensual' || prorratear) ? cuotas / (CUOTAS_POR_MES[tipoPago] || 1) : 1;
    const total = Math.round(monto + monto * (interes / 100) * meses);

    const valorCuota = Math.round(total / cuotas);

    $('#monto_total_p').val(total);
    $('#monto_pendiente_p').val(total);
    $('#valor_cuota_p').val(valorCuota);

    // Actualizar "dinero a entregar" en modal de refinanciamiento
    if (refiPendiente > 0) {
        const entrega = monto - refiPendiente;
        $('#refi_entrega_label').text('$' + Math.round(entrega).toLocaleString('es-CO'));
        if (entrega > 0) {
            $('#refi-entrega-row').show().removeClass('alert-warning').addClass('alert-success');
        } else if (entrega < 0) {
            $('#refi-entrega-row').show().removeClass('alert-success').addClass('alert-warning');
        } else {
            $('#refi-entrega-row').hide();
        }
    }
}

/* ── Inicializar DataTable ──────────────────────────────────────────────── */
function iniciarTabla() {
    if (tablaIniciada) {
        $('#tabla-prestamos').DataTable().ajax.reload();
        return;
    }

    tablaIniciada = true;

    $('#skeleton-prestamos').hide();
    $('#wrapper-prestamos').show();

    const dt = $('#tabla-prestamos').DataTable({
        language:    idioma,
        processing:  true,
        responsive:  true,
        lengthMenu:  [[25, 50, 100, -1], [25, 50, 100, 'Todos']],
        aaSorting:   [[1, 'asc']],

        ajax: {
            url: window.V2_PRESTAMO_URL,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        },

        columns: [
            { data: 'action',           name: 'action',           orderable: false, searchable: false },
            { data: 'consecutivo',      name: 'consecutivo' },
            { data: 'idp',              name: 'idp' },
            { data: 'nombres',          name: 'nombres' },
            { data: 'apellidos',        name: 'apellidos' },
            { data: 'monto',            name: 'monto' },
            { data: 'monto_total',      name: 'monto_total' },
            { data: 'monto_pendiente',  name: 'monto_pendiente' },
            { data: 'monto_atrasado',   name: 'monto_atrasado' },
            { data: 'cuotas_atrasadas', name: 'cuotas_atrasadas' },
            { data: 'tipo_pago',        name: 'tipo_pago' },
            { data: 'cuotas',           name: 'cuotas' },
            { data: 'cuotas_pendientes',name: 'cuotas_pendientes' },
            { data: 'interes',          name: 'interes' },
            { data: 'valor_cuota',      name: 'valor_cuota' },
            { data: 'fecha_inicial',    name: 'fecha_inicial' },
            { data: 'estado_badge',     name: 'estado_badge',     orderable: false, searchable: false },
        ],

        dom: '<"row"<"col-md-4"l><"col-md-5"f><"col-md-3"B>>rt<"row"<"col-md-8"i><"col-md-4"p>>',

        buttons: [
            { extend: 'copyHtml5',  className: 'btn btn-outline-primary  btn-sm', titleAttr: 'Copiar' },
            { extend: 'excelHtml5', className: 'btn btn-outline-success  btn-sm', titleAttr: 'Excel' },
            { extend: 'csvHtml5',   className: 'btn btn-outline-warning  btn-sm', titleAttr: 'CSV' },
            { extend: 'pdfHtml5',   className: 'btn btn-outline-secondary btn-sm', titleAttr: 'PDF' },
        ],

        // Colorear filas por estado usando clases CSS (no inline style)
        createdRow(row, data) {
            if (data.monto_atrasado > 0) {
                $(row).addClass('row-atrasado');
            } else if (data.monto_pendiente == 0) {
                $(row).addClass('row-pagado');
            }
        },

    });

    return dt;
}

/* ── Document ready ─────────────────────────────────────────────────────── */
$(function () {

    // ── Select2 — inicializar dentro de cada modal con dropdownParent
    //    (evita que el dropdown quede detrás del backdrop de Bootstrap)
    function initSelect2EnModal(modalId) {
        /* .not('.select2-hidden-accessible') evita doble-init */
        $('#' + modalId + ' .select2bs4').not('.select2-hidden-accessible').select2({
            theme:          'bootstrap4',
            dropdownParent: $('#' + modalId),
        });
    }
    $('#modal-crear-prestamo').on('shown.bs.modal', function () { initSelect2EnModal('modal-crear-prestamo'); });
    $('#modal-refinanciar').on('shown.bs.modal',    function () { initSelect2EnModal('modal-refinanciar'); });

    // ── Iniciar DataTable ────────────────────────────────────────────────────
    iniciarTabla();

    // ── Recalcular montos en cada cambio de inputs ───────────────────────────
    $('#monto_p, #cuotas_p, #interes_p, #tipo_pago_p, #interes_prorrateado_p').on('change input', calcularMontos);

    // ── Abrir modal crear ────────────────────────────────────────────────────
    $('#btn-crear-prestamo').on('click', function () {
        refiPendiente = 0; // asegurar que calcularMontos no toque el bloque de entrega
        $('#form-crear-prestamo')[0].reset();
        $('#form-result-crear').html('');
        $('#monto_total_p, #valor_cuota_p, #monto_pendiente_p').val('');
        $('#modal-crear-prestamo .select2bs4').val(null).trigger('change');
        $('#modal-crear-prestamo').modal('show');
    });

    // ── Envío del formulario (registrado UNA SOLA VEZ fuera del click) ───────
    // Fix: en el original se registraba dentro del handler del click,
    //      acumulando N listeners con cada apertura del modal.
    $('#form-crear-prestamo').on('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Crear el préstamo?',
            text:  'Se generarán las cuotas automáticamente.',
            icon:  'question',
            showCancelButton:  true,
            confirmButtonText: 'Sí, crear',
            cancelButtonText:  'Cancelar',
        }).then(result => {
            if (!result.value) return;

            $('#loader-crear').addClass('active');
            $('#btn-guardar-prestamo').prop('disabled', true);

            $.ajax({
                url:      window.V2_GUARDAR_URL,
                method:   'POST',
                data:     $(this).serialize(),
                dataType: 'json',
                success(data) {
                    if (data.errors) {
                        const html = data.errors
                            .map(e => `<div class="alert alert-danger py-1 mb-1">${e}</div>`)
                            .join('');
                        $('#form-result-crear').html(html);
                        return;
                    }

                    $('#modal-crear-prestamo').modal('hide');
                    $('#tabla-prestamos').DataTable().ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: 'Préstamo creado correctamente',
                        showConfirmButton: false,
                        timer: 1600,
                    });
                },
                error(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error inesperado. Intente de nuevo.';
                    $('#form-result-crear').html(
                        `<div class="alert alert-danger py-1 mb-1">${msg}</div>`
                    );
                },
                complete() {
                    $('#loader-crear').removeClass('active');
                    $('#btn-guardar-prestamo').prop('disabled', false);
                },
            });
        });
    });

    // ── Ver detalle de cuotas ────────────────────────────────────────────────
    $(document).on('click', '.detalle', function () {
        const id = $(this).data('id');
        $('#body-cuotas').empty();

        $.ajax({
            url:      window.V2_BASE_URL + '/prestamo/' + id + '/cuotas',
            dataType: 'json',
            success(data) {
                const estados = { C: 'Pendiente', P: 'Pagada', A: 'Anulada', T: 'Transferida' };
                const clases  = { C: 'warning', P: 'success', A: 'danger', T: 'info' };

                data.result.forEach(c => {
                    const badge = `<span class="badge badge-${clases[c.estado] || 'secondary'}">
                                    ${estados[c.estado] || c.estado}
                                   </span>`;
                    $('#body-cuotas').append(`
                        <tr>
                            <td>${c.d_numero_cuota}</td>
                            <td>$${parseFloat(c.valor_cuota).toLocaleString('es-CO')}</td>
                            <td>${c.fecha_cuota}</td>
                            <td>${c.valor_cuota_pagada
                                ? '$' + parseFloat(c.valor_cuota_pagada).toLocaleString('es-CO')
                                : '—'}</td>
                            <td>${badge}</td>
                        </tr>
                    `);
                });

                $('#modal-cuotas').modal('show');
            },
            error() {
                Swal.fire('Error', 'No se pudo cargar el detalle de cuotas.', 'error');
            },
        });
    });

    // ── Ver detalle de pagos ─────────────────────────────────────────────────
    $(document).on('click', '.pagos', function () {
        const id = $(this).data('id');
        $('#body-pagos').empty();

        $.ajax({
            url:      window.V2_BASE_URL + '/pago-card/' + id,
            dataType: 'json',
            success(data) {
                if (!data.result1 || data.result1.length === 0) {
                    $('#body-pagos').append(
                        '<tr><td colspan="6" class="text-center text-muted">Sin pagos registrados.</td></tr>'
                    );
                } else {
                    data.result1.forEach(p => {
                        $('#body-pagos').append(`
                            <tr>
                                <td>${p.prestamo_id}</td>
                                <td>${p.numero_cuota}</td>
                                <td>$${parseFloat(p.valor_abono).toLocaleString('es-CO')}</td>
                                <td>${p.observacion_pago || '—'}</td>
                                <td>${p.fecha_pago}</td>
                                <td>${p.created_at}</td>
                            </tr>
                        `);
                    });
                }

                $('#modal-pagos').modal('show');
            },
            error() {
                Swal.fire('Error', 'No se pudo cargar el detalle de pagos.', 'error');
            },
        });
    });

    // ── Abrir modal refinanciar ──────────────────────────────────────────
    $(document).on('click', '.refinanciar', function () {
        const id = $(this).data('id');
        $('#form-refinanciar')[0].reset();
        $('#form-result-refi').html('');
        $('#refi-entrega-row').hide();
        refiPendiente = 0;
        $.get(window.V2_BASE_URL + '/prestamo/' + id + '/refinanciar', function (data) {
            if (!data.result || !data.result.length) {
                Swal.fire('Error', 'No se pudo cargar el préstamo.', 'error');
                return;
            }
            const r             = data.result[0];
            const cuotasRestantes = data.result.length; // cuotas pendientes/atrasadas
            refiPendiente       = parseFloat(r.monto_pendiente) || 0;

            $('#refi-idp').text('#' + r.idp);
            $('#refi_prestamo_id').val(r.idp);
            $('#refi_numero_cuota').val(r.d_numero_cuota);
            $('#refi_valor_cuota').val(r.valor_cuota);
            $('#refi_fecha_pago').val(new Date().toISOString().split('T')[0]);
            $('#refi_usuario_id').val(r.usuario_id);
            $('#refi_saldo_label').text('$' + refiPendiente.toLocaleString('es-CO'));
            // Pre-llenar valor abono con el saldo total para cerrar el préstamo
            $('#refi_valor_abono').val(refiPendiente);
            // Pre-llenar campos del nuevo préstamo
            $('#cliente_id_p').val(r.cliente_id).trigger('change');
            $('#tipo_pago_p').val(r.tipo_pago).trigger('change');
            $('#cuotas_p').val(cuotasRestantes);
            $('#interes_p').val(r.interes);
            $('#usuario_id_p').val(r.usuario_id).trigger('change');
            // Recalcular totales si ya hay monto (ej. al reabrir el modal)
            calcularMontos();
            $('#modal-refinanciar').modal('show');
        }).fail(function () {
            Swal.fire('Error', 'No se pudo cargar el préstamo.', 'error');
        });
    });

    // ── Envío del formulario de refinanciamiento ─────────────────────────
    $('#form-refinanciar').on('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Refinanciar el préstamo?',
            text: 'Se cerrará el préstamo actual y se creará uno nuevo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, refinanciar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (!result.value) return;
            $('#loader-refi').addClass('active');
            $('#btn-guardar-refi').prop('disabled', true);
            $.ajax({
                url:      window.V2_REFI_URL,
                method:   'POST',
                data:     $(this).serialize(),
                dataType: 'json',
                success(data) {
                    if (data.errors) {
                        const html = data.errors.map(e => `<div class="alert alert-danger py-1 mb-1">${e}</div>`).join('');
                        $('#form-result-refi').html(html);
                        return;
                    }
                    $('#modal-refinanciar').modal('hide');
                    $('#tabla-prestamos').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Préstamo refinanciado correctamente', timer: 1600, showConfirmButton: false });
                },
                error(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error inesperado.';
                    $('#form-result-refi').html(`<div class="alert alert-danger py-1 mb-1">${msg}</div>`);
                },
                complete() {
                    $('#loader-refi').removeClass('active');
                    $('#btn-guardar-refi').prop('disabled', false);
                },
            });
        });
    });

    // ── Anular préstamo ──────────────────────────────────────────────────
    $(document).on('click', '.anularp', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: '¿Anular este préstamo?',
            text: 'El préstamo quedará inactivo y no podrá revertirse.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            confirmButtonColor: '#dc3545',
            cancelButtonText: 'Cancelar',
        }).then(function (result) {
            if (!result.value) return;
            $.ajax({
                url:    window.V2_BASE_URL + '/prestamo/' + id + '/anular',
                method: 'POST',
                data:   { _method: 'PUT', _token: window.V2_CSRF },
                dataType: 'json',
                success: function () {
                    $('#tabla-prestamos').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Préstamo anulado.', timer: 1500, showConfirmButton: false });
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo anular el préstamo.', 'error');
                },
            });
        });
    });

});
