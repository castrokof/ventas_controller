/**
 * assets/pages/scripts/admin/pagocalender/index.js
 *
 * Lógica frontend para el módulo Pago Card V2.
 *
 * Tabs:
 *  - #tab-pagos      → tabla #pago       (indexc  — filtro por estado_pago)
 *  - #tab-prestamos  → tabla #prestamos  (indexcp — préstamos con saldo > 0)
 *  - #tab-clientes   → tabla #clientecard (clientes del usuario)
 *  - #tab-anulados   → tabla #anulados   (préstamos anulados — skeleton oculto)
 *
 * Clases de botones generados por el servidor:
 *  pay       → registrar pago (por idd de cuota)
 *  payp      → registrar pago (por idp de préstamo + fecha)
 *  pagosr    → ver pagos registrados de un préstamo
 *  editpay   → editar pago ya registrado
 *  detallepay→ ver pagos realizados al crédito
 *  adelantoc → ver cuotas adelantables del crédito
 *  atrasosp  → ver cuotas atrasadas del crédito
 *  detalle   → ver cuotas del crédito en modal-d
 */

/* ── Idioma DataTables ──────────────────────────────────────────────────────── */
const idioma = {
    sProcessing:   'Procesando...',
    sLengthMenu:   'Mostrar _MENU_ registros',
    sZeroRecords:  'No se encontraron resultados',
    sEmptyTable:   'Ningún dato disponible',
    sInfo:         'Mostrando _START_ a _END_ de _TOTAL_ registros',
    sInfoEmpty:    'Mostrando 0 a 0 de 0 registros',
    sInfoFiltered: '(filtrado de _MAX_ total)',
    sSearch:       'Buscar:',
    sLoadingRecords: 'Cargando...',
    oPaginate: { sFirst: 'Primero', sLast: 'Último', sNext: 'Siguiente', sPrevious: 'Anterior' },
    oAria: { sSortAscending: ': orden ascendente', sSortDescending: ': orden descendente' },
    buttons: { copy: 'Copiar', colvis: 'Columnas' },
};

/* ── Estado de las tablas ───────────────────────────────────────────────────── */
let tablaPagoIniciada      = false;
let tablaPrestamoIniciada  = false;
let tablaClientesIniciada  = false;
let dtPago = null;

/* ── Base URL V2 ────────────────────────────────────────────────────────────── */
const BASE_PC = '/admin/v2/pago-card';

/* ════════════════════════════════════════════════════════════════════════════
 * DOCUMENT READY
 * ════════════════════════════════════════════════════════════════════════════ */
$(function () {

    /* ── Select2 ──────────────────────────────────────────────────────────── */
    $('.select2bs4').select2({ theme: 'bootstrap4' });

    /* ── Tab Pagos: carga inicial con filtro 0 (por cobrar del día) ────────── */
    $('#estado_pago').val('0').trigger('change');

    /* ── Cambio de filtro en Tab Pagos ────────────────────────────────────── */
    $('#estado_pago').on('change', function () {
        const val = $(this).val();
        if (val === '') {
            $('#skeleton-pago').hide();
            $('#wrapper-pago').show();
            return;
        }
        if (!tablaPagoIniciada) {
            iniciarTablaPago(val);
        } else {
            dtPago.ajax.reload();
        }
    });

    /* ── Lazy-load tabs ───────────────────────────────────────────────────── */
    $('#tab-prestamos-link').one('click', iniciarTablaPrestamoCard);
    $('#tab-clientes-link').one('click', iniciarTablaClientes);
    $('#tab-anulados-link').one('click', function () {
        $('#skeleton-anulados').hide();
        $('#wrapper-anulados').show();
        $('#empty-anulados').show();
    });

    /* ════════════════════════════════════════════════════════════════════════
     * BOTONES GENERADOS POR EL SERVIDOR
     * ════════════════════════════════════════════════════════════════════════ */

    /* ── pay: registrar pago por cuota individual (idd) ───────────────────── */
    $(document).on('click', '.pay', function () {
        const id = $(this).attr('id') || $(this).data('id');
        $.ajax({
            url: BASE_PC + '/' + id + '/edit',
            dataType: 'json',
            success(data) {
                if (!data.result || data.result.length === 0) return;
                const d = data.result[0];
                rellenarModalPago(d);
                $('#form-general').data('modo', 'crear').removeData('pid');
                $('.modal-title-pd').text('Registrar Pago — Cuota #' + (d.d_numero_cuota || ''));
                $('#modal-pd').modal('show');
            },
            error() { Swal.fire('Error', 'No se pudo cargar la cuota.', 'error'); },
        });
    });

    /* ── payp / pagosr: registrar pago por préstamo (idp + fecha) ─────────── */
    $(document).on('click', '.payp, .pagosr', function () {
        const id  = $(this).attr('id');
        const idf = $(this).attr('idf');
        $.ajax({
            url: BASE_PC + '/' + id + '/editarp',
            data: { idf: idf },
            dataType: 'json',
            success(data) {
                if (!data.result || data.result.length === 0) return;
                const d = data.result[0];
                rellenarModalPago(d);
                $('#form-general').data('modo', 'crear').removeData('pid');
                $('.modal-title-pd').text('Registrar Pago — Crédito #' + id);
                $('#modal-pd').modal('show');
            },
            error() { Swal.fire('Error', 'No se pudo cargar el préstamo.', 'error'); },
        });
    });

    /* ── editpay: editar pago ya registrado ───────────────────────────────── */
    $(document).on('click', '.editpay', function () {
        const id = $(this).attr('id') || $(this).data('id');
        $.ajax({
            url: BASE_PC + '/' + id + '/editpay',
            dataType: 'json',
            success(data) {
                if (!data.result) return;
                const d = data.result;
                rellenarModalPago(d);
                $('#form-general').data('modo', 'editar').data('pid', id);
                $('.modal-title-pd').text('Editar Pago — Cuota #' + (d.d_numero_cuota || ''));
                $('#modal-pd').modal('show');
            },
            error() { Swal.fire('Error', 'No se pudo cargar el pago.', 'error'); },
        });
    });

    /* ── detalle: cuotas del préstamo en modal-d ──────────────────────────── */
    $(document).on('click', '.detalle', function () {
        const id = $(this).attr('id');
        $('.modal-title-d').text('Cuotas del Crédito #' + id);

        if ($.fn.DataTable.isDataTable('#detalleCuota')) {
            $('#detalleCuota').DataTable().destroy();
        }
        $('#detalleCuota').DataTable({
            language:   idioma,
            processing: true,
            serverSide: true,
            ajax: { url: '/admin/v2/prestamo/' + id + '/cuotas' },
            columns: [
                { data: 'd_numero_cuota', title: '# Cuota' },
                { data: 'valor_cuota',    title: 'Valor $' },
                { data: 'fecha_cuota',    title: 'Fecha' },
                { data: 'valor_cuota_pagada', title: 'Pagado $', defaultContent: '—' },
                { data: 'estado',         title: 'Estado' },
            ],
        });
        $('#modal-d').modal('show');
    });

    /* ── detallepay: pagos realizados al crédito en modal-dp ─────────────── */
    $(document).on('click', '.detallepay', function () {
        const id = $(this).attr('id');
        $('.modal-title-dp').text('Pagos realizados — Crédito #' + id);
        $('#btnar').html('');
        $('#detalles').html('<p class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</p>');
        $('#modal-dp').modal('show');

        $.ajax({
            url: BASE_PC + '/' + id,
            dataType: 'json',
            success(data) {
                const pagos = data.result1 || [];
                if (pagos.length === 0) {
                    $('#detalles').html('<p class="text-muted text-center py-3">Sin pagos registrados.</p>');
                    return;
                }
                let html = '<table class="table table-sm table-striped table-bordered">';
                html += '<thead class="thead-light"><tr>';
                html += '<th># Cuota</th><th>Abono $</th><th>Fecha pago</th><th>Observación</th>';
                html += '</tr></thead><tbody>';
                pagos.forEach(function (p) {
                    html += '<tr>';
                    html += '<td>' + p.numero_cuota + '</td>';
                    html += '<td>$' + parseFloat(p.valor_abono).toLocaleString('es-CO') + '</td>';
                    html += '<td>' + (p.fecha_pago || '—') + '</td>';
                    html += '<td>' + (p.observacion_pago || '—') + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#detalles').html(html);
            },
            error() {
                $('#detalles').html('<p class="text-danger">Error al cargar los pagos.</p>');
            },
        });
    });

    /* ── adelantoc: cuotas adelantables en modal-acuotas ─────────────────── */
    $(document).on('click', '.adelantoc', function () {
        const id = $(this).attr('id');
        $('.modal-title-acuotas').text('Adelanto de Cuotas — Crédito #' + id);

        if ($.fn.DataTable.isDataTable('#pagoa')) $('#pagoa').DataTable().destroy();
        $('#pagoa').DataTable({
            language:   idioma,
            processing: true,
            serverSide: true,
            ajax: { url: BASE_PC + '/adelanto', data: { prestamoc_id: id } },
            columns: [
                { data: 'action',        orderable: false, searchable: false, defaultContent: '' },
                { data: 'd_numero_cuota', title: '# Cuota' },
                { data: 'fecha_cuota',   title: 'Fecha' },
                { data: 'estado',        title: 'Estado' },
                { data: 'nombres',       title: 'Nombres' },
                { data: 'apellidos',     title: 'Apellidos' },
                { data: 'idp',           title: 'Id Préstamo' },
            ],
        });
        $('#modal-acuotas').modal('show');
    });

    /* ── atrasosp: cuotas atrasadas en modal-atrasosp ─────────────────────── */
    $(document).on('click', '.atrasosp', function () {
        const id = $(this).attr('id');
        $('.modal-title-atrasosp').text('Cuotas Atrasadas — Crédito #' + id);

        if ($.fn.DataTable.isDataTable('#atrasosp')) $('#atrasosp').DataTable().destroy();
        $('#atrasosp').DataTable({
            language:   idioma,
            processing: true,
            serverSide: true,
            ajax: { url: BASE_PC + '/atrasos', data: { prestamoc_id: id } },
            columns: [
                { data: 'action',        orderable: false, searchable: false, defaultContent: '' },
                { data: 'd_numero_cuota', title: '# Cuota' },
                { data: 'fecha_cuota',   title: 'Fecha' },
                { data: 'estado',        title: 'Estado' },
                { data: 'nombres',       title: 'Nombres' },
                { data: 'apellidos',     title: 'Apellidos' },
                { data: 'idp',           title: 'Id Préstamo' },
            ],
        });
        $('#modal-atrasosp').modal('show');
    });

    /* ════════════════════════════════════════════════════════════════════════
     * FORMULARIO DE PAGO
     * ════════════════════════════════════════════════════════════════════════ */

    /* ── Switch: cambiar fecha de cuota ───────────────────────────────────── */
    $('#customSwitch1').on('change', function () {
        if ($(this).is(':checked')) {
            $('#chance_fecha').show();
        } else {
            $('#chance_fecha').hide();
            $('#new_date').val('');
        }
    });

    /* ── Submit del form (registrar / actualizar) — listener registrado UNA VEZ */
    $('#form-general').on('submit', function (e) {
        e.preventDefault();

        const modo = $(this).data('modo') || 'crear';
        const pid  = $(this).data('pid');           // idd de la cuota (modo editar)
        const url  = (modo === 'editar')
            ? BASE_PC + '/' + pid
            : BASE_PC + '/guardar';

        Swal.fire({
            title: modo === 'editar' ? '¿Actualizar pago?' : '¿Registrar pago?',
            icon: 'question',
            showCancelButton:  true,
            confirmButtonText: 'Sí',
            cancelButtonText:  'Cancelar',
        }).then(function (res) {
            if (!res.value) return;

            const data = $(e.target).serialize()
                       + (modo === 'editar' ? '&_method=PUT' : '');

            $.ajax({
                url:      url,
                method:   'POST',
                data:     data,
                dataType: 'json',
                success(resp) {
                    const mensajes = {
                        ok:          'Pago registrado correctamente.',
                        total:       'Crédito cancelado en su totalidad.',
                        okadelanto:  'Adelanto registrado correctamente.',
                        abono:       'Abono parcial registrado.',
                        okca:        'Pago de cuota atrasada registrado.',
                        abonoa:      'Abono a cuota atrasada registrado.',
                        noat:        'Pago registrado y atraso saldado.',
                        oka:         'Pago actualizado correctamente.',
                        noadelanto:  'El abono supera el valor de la cuota.',
                        error:       'No se pudo procesar el pago.',
                        noa:         'El abono no corresponde al monto esperado.',
                        adelantos:   'Para adelantar cuotas use "Add Cuotas".',
                        vcda:        'El abono debe ser igual al atraso + valor cuota.',
                        adelantosa:  'El abono supera el monto permitido.',
                        okcaerror:   'Error al procesar la cuota atrasada.',
                    };
                    const errores = ['error','noadelanto','noa','adelantos','vcda','adelantosa','okcaerror','noat'];
                    const esError = errores.includes(resp.success);

                    Swal.fire({
                        icon: esError ? 'warning' : 'success',
                        title: mensajes[resp.success] || 'Operación completada.',
                        showConfirmButton: false,
                        timer: 2200,
                    });

                    if (!esError) {
                        $('#modal-pd').modal('hide');
                        if (dtPago) dtPago.ajax.reload();
                        if ($.fn.DataTable.isDataTable('#prestamos')) {
                            $('#prestamos').DataTable().ajax.reload();
                        }
                    }
                },
                error() {
                    Swal.fire('Error', 'No se pudo registrar el pago.', 'error');
                },
            });
        });
    });

    /* ── Limpiar modal al cerrar ──────────────────────────────────────────── */
    $('#modal-pd').on('hidden.bs.modal', function () {
        $('#form-general')[0].reset();
        $('#form-general').removeData('modo').removeData('pid');
        $('#form_result').html('');
        $('#chance_fecha').hide();
        $('#customSwitch1').prop('checked', false);
    });

});

/* ════════════════════════════════════════════════════════════════════════════
 * FUNCIONES DE INICIALIZACIÓN DE TABLAS
 * ════════════════════════════════════════════════════════════════════════════ */

/** Tab Pagos — inicializa DataTable con el filtro estado_pago activo. */
function iniciarTablaPago(estadoPago) {
    tablaPagoIniciada = true;

    dtPago = $('#pago').DataTable({
        language:   idioma,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: BASE_PC + '/tab',
            data: function (d) {
                d.estado_pago = $('#estado_pago').val();
            },
        },
        columns: [
            { data: 'action', orderable: false, searchable: false, defaultContent: '' },
            { data: 'datos',  orderable: false, searchable: false, defaultContent: '' },
            { data: 'idp',    name: 'prestamo.idp', defaultContent: '' },
        ],
        initComplete: function () {
            $('#skeleton-pago').hide();
            $('#wrapper-pago').show();
        },
    });
}

/** Tab Préstamos — DataTable con todos los préstamos activos del usuario. */
function iniciarTablaPrestamoCard() {
    if (tablaPrestamoIniciada) return;
    tablaPrestamoIniciada = true;

    $('#prestamos').DataTable({
        language:   idioma,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: { url: BASE_PC },
        columns: [
            { data: 'action', orderable: false, searchable: false, defaultContent: '' },
            { data: 'datos',  orderable: false, searchable: false, defaultContent: '' },
            { data: 'idp',    name: 'prestamo.idp', defaultContent: '' },
        ],
        initComplete: function () {
            $('#skeleton-prestamos').hide();
            $('#wrapper-prestamos').show();
        },
    });
}

/** Tab Clientes — DataTable con clientes del usuario (card view). */
function iniciarTablaClientes() {
    if (tablaClientesIniciada) return;
    tablaClientesIniciada = true;

    $('#clientecard').DataTable({
        language:   idioma,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: { url: '/clientes_card' },
        columns: [
            { data: 'datos', orderable: false, searchable: false, defaultContent: '' },
            { data: 'consecutivo', name: 'consecutivo', defaultContent: '' },
        ],
        initComplete: function () {
            $('#skeleton-clientes').hide();
            $('#wrapper-clientes').show();
        },
    });
}

/* ════════════════════════════════════════════════════════════════════════════
 * HELPER: rellenar modal de pago
 * ════════════════════════════════════════════════════════════════════════════ */
function rellenarModalPago(d) {
    $('#nombres').val((d.nombres || '') + ' ' + (d.apellidos || ''));
    $('#tipo_pago').val(d.tipo_pago || '');
    $('#idp').val(d.idp || d.prestamo_id || '');
    $('#fecha_cuota').val(d.fecha_cuota || '');
    $('#n_cuota').val(d.d_numero_cuota || '');
    $('#valor_cuota').val(d.valor_cuota || '');
    $('#estado_cuota').val(d.estado || '');
    $('#vatraso').val(d.monto_atrasado || 0);
    $('#valor_abono').val('');
    $('#observacion').val('');
}
