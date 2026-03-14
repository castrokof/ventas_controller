/**
 * assets/pages/scripts/admin/pagocalender/index.js
 *
 * Lógica frontend para el módulo Pago Card V2.
 *
 * Tabs:
 *  - #tab-pagos      → tabla #pago        (indexc  — filtro por estado_pago)
 *  - #tab-prestamos  → tabla #prestamos   (indexcp — préstamos con saldo > 0)
 *  - #tab-clientes   → tabla #clientecard (clientes del usuario)
 *  - #tab-anulados   → (sin backend aún, muestra estado vacío)
 *
 * Clases de botones generados por el servidor:
 *  pay        → registrar pago por cuota (idd)
 *  payp/pagosr→ registrar pago por préstamo (idp + fecha)
 *  editpay    → editar pago registrado
 *  detallepay → pagos realizados al crédito (modal-dp)
 *  adelantoc  → cuotas adelantables (modal-acuotas)
 *  atrasosp   → cuotas atrasadas (modal-atrasosp)
 *  detalle    → cuotas del crédito (modal-d)
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
    oAria: { sSortAscending: ': ascendente', sSortDescending: ': descendente' },
    buttons: { copy: 'Copiar', colvis: 'Columnas' },
};

/* ── Estado de las tablas ───────────────────────────────────────────────────── */
let tablaPagoIniciada     = false;
let tablaPresIniciada     = false;
let tablaCliIniciada      = false;
let dtPago = null;

/* ── Base URL V2 ────────────────────────────────────────────────────────────── */
const BASE_PC = '/admin/v2/pago-card';

/* ── Columnas comunes para tablas de prestamo/cuota ────────────────────────── */
const COL_CARD = [
    { data: 'action', orderable: false, searchable: false, defaultContent: '' },
    { data: 'datos',  orderable: false, searchable: false, defaultContent: '' },
    { data: 'idp',    name: 'prestamo.idp', defaultContent: '' },
];

/* ════════════════════════════════════════════════════════════════════════════
 * DOCUMENT READY
 * ════════════════════════════════════════════════════════════════════════════ */
$(function () {

    /* ── Select2 ──────────────────────────────────────────────────────────── */
    $('.select2bs4').select2({ theme: 'bootstrap4' });

    /* ── Tab Pagos activo por defecto: mostrar wrapper y crear DataTable ─── */
    mostrarTab('pago');
    $('#estado_pago').val('0');
    iniciarTablaPago();

    /* ── Lazy-load tabs (usando shown.bs.tab para que el DOM esté visible) ── */
    $('#tab-prestamos-link').on('shown.bs.tab', function () {
        if (!tablaPresIniciada) iniciarTablaPrestamoCard();
    });
    $('#tab-clientes-link').on('shown.bs.tab', function () {
        if (!tablaCliIniciada) iniciarTablaClientes();
    });
    $('#tab-anulados-link').on('shown.bs.tab', function () {
        mostrarTab('anulados');
        $('#empty-anulados').show();
    });

    /* ── Cambio de filtro en Tab Pagos ────────────────────────────────────── */
    $('#estado_pago').on('change', function () {
        if ($(this).val() === '') return;
        if (dtPago) {
            dtPago.ajax.reload();
        }
    });

    /* ════════════════════════════════════════════════════════════════════════
     * BOTONES GENERADOS POR EL SERVIDOR
     * ════════════════════════════════════════════════════════════════════════ */

    /* ── pay: registrar pago por cuota individual (idd) ───────────────────── */
    $(document).on('click', '.pay', function () {
        const id = $(this).attr('id') || $(this).data('id');
        $.get(BASE_PC + '/' + id + '/edit', function (data) {
            if (!data.result || data.result.length === 0) return;
            rellenarModalPago(data.result[0]);
            $('#form-general').data('modo', 'crear').removeData('pid');
            $('.modal-title-pd').text('Registrar Pago — Cuota #' + (data.result[0].d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar la cuota.', 'error'); });
    });

    /* ── payp / pagosr: registrar pago por préstamo (idp + fecha) ─────────── */
    $(document).on('click', '.payp, .pagosr', function () {
        const id  = $(this).attr('id');
        const idf = $(this).attr('idf');
        $.get(BASE_PC + '/' + id + '/editarp', { idf: idf }, function (data) {
            if (!data.result || data.result.length === 0) return;
            rellenarModalPago(data.result[0]);
            $('#form-general').data('modo', 'crear').removeData('pid');
            $('.modal-title-pd').text('Registrar Pago — Crédito #' + id);
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar el préstamo.', 'error'); });
    });

    /* ── editpay: editar pago ya registrado ───────────────────────────────── */
    $(document).on('click', '.editpay', function () {
        const id = $(this).attr('id') || $(this).data('id');
        $.get(BASE_PC + '/' + id + '/editpay', function (data) {
            if (!data.result) return;
            rellenarModalPago(data.result);
            $('#form-general').data('modo', 'editar').data('pid', id);
            $('.modal-title-pd').text('Editar Pago — Cuota #' + (data.result.d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar el pago.', 'error'); });
    });

    /* ── detalle: cuotas del préstamo en modal-d ──────────────────────────── */
    $(document).on('click', '.detalle', function () {
        const id = $(this).attr('id');
        $('.modal-title-d').text('Cuotas del Crédito #' + id);
        if ($.fn.DataTable.isDataTable('#detalleCuota')) $('#detalleCuota').DataTable().destroy();
        $('#detalleCuota').DataTable({
            language:   idioma,
            processing: true,
            serverSide: true,
            ajax: { url: '/admin/v2/prestamo/' + id + '/cuotas' },
            columns: [
                { data: 'd_numero_cuota', title: '# Cuota', defaultContent: '' },
                { data: 'valor_cuota',    title: 'Valor $',  defaultContent: '' },
                { data: 'fecha_cuota',    title: 'Fecha',    defaultContent: '' },
                { data: 'valor_cuota_pagada', title: 'Pagado $', defaultContent: '—' },
                { data: 'estado',         title: 'Estado',   defaultContent: '' },
            ],
        });
        $('#modal-d').modal('show');
    });

    /* ── detallepay: pagos realizados al crédito en modal-dp ─────────────── */
    $(document).on('click', '.detallepay', function () {
        const id = $(this).attr('id');
        $('.modal-title-dp').text('Pagos realizados — Crédito #' + id);
        $('#detalles').html('<p class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</p>');
        $('#modal-dp').modal('show');
        $.get(BASE_PC + '/' + id, function (data) {
            const pagos = data.result1 || [];
            if (pagos.length === 0) {
                $('#detalles').html('<p class="text-muted text-center py-3">Sin pagos registrados.</p>');
                return;
            }
            let html = '<table class="table table-sm table-striped table-bordered"><thead class="thead-light"><tr>';
            html += '<th># Cuota</th><th>Abono $</th><th>Fecha</th><th>Observación</th>';
            html += '</tr></thead><tbody>';
            pagos.forEach(function (p) {
                html += '<tr><td>' + p.numero_cuota + '</td>';
                html += '<td>$' + parseFloat(p.valor_abono).toLocaleString('es-CO') + '</td>';
                html += '<td>' + (p.fecha_pago || '—') + '</td>';
                html += '<td>' + (p.observacion_pago || '—') + '</td></tr>';
            });
            html += '</tbody></table>';
            $('#detalles').html(html);
        }).fail(function () { $('#detalles').html('<p class="text-danger">Error al cargar.</p>'); });
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
                { data: 'action',         orderable: false, searchable: false, defaultContent: '' },
                { data: 'd_numero_cuota', title: '# Cuota',    defaultContent: '' },
                { data: 'fecha_cuota',    title: 'Fecha',      defaultContent: '' },
                { data: 'estado',         title: 'Estado',     defaultContent: '' },
                { data: 'nombres',        title: 'Nombres',    defaultContent: '' },
                { data: 'apellidos',      title: 'Apellidos',  defaultContent: '' },
                { data: 'idp',            title: 'Id Crédito', defaultContent: '' },
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
                { data: 'action',         orderable: false, searchable: false, defaultContent: '' },
                { data: 'd_numero_cuota', title: '# Cuota',    defaultContent: '' },
                { data: 'fecha_cuota',    title: 'Fecha',      defaultContent: '' },
                { data: 'estado',         title: 'Estado',     defaultContent: '' },
                { data: 'nombres',        title: 'Nombres',    defaultContent: '' },
                { data: 'apellidos',      title: 'Apellidos',  defaultContent: '' },
                { data: 'idp',            title: 'Id Crédito', defaultContent: '' },
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

    /* ── Submit del formulario (registrar / actualizar) ─────────────────── */
    $('#form-general').on('submit', function (e) {
        e.preventDefault();

        const modo = $(this).data('modo') || 'crear';
        const pid  = $(this).data('pid');
        const url  = (modo === 'editar') ? BASE_PC + '/' + pid : BASE_PC + '/guardar';

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
                success: function (resp) {
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
                    const errores = ['error','noadelanto','noa','adelantos','vcda','adelantosa','okcaerror'];
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
                error: function () {
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
 * HELPERS DE INICIALIZACIÓN DE TABLAS
 * ════════════════════════════════════════════════════════════════════════════ */

/**
 * Oculta el skeleton y muestra el wrapper de un tab.
 * Se llama ANTES de crear el DataTable para evitar que el wrapper
 * oculto impida que DataTables calcule el tamaño de las columnas.
 */
function mostrarTab(nombre) {
    $('#skeleton-' + nombre).hide();
    $('#wrapper-' + nombre).show();
}

/** Tab Pagos — DataTable con filtro estado_pago dinámico. */
function iniciarTablaPago() {
    if (tablaPagoIniciada) return;
    tablaPagoIniciada = true;

    /* Mostrar wrapper antes de crear la tabla */
    mostrarTab('pago');

    dtPago = $('#pago').DataTable({
        language:   idioma,
        processing: true,
        responsive: true,
        ajax: {
            url: BASE_PC + '/tab',
            type: 'get',
            data: function () {
                return { estado_pago: $('#estado_pago').val() };
            },
        },
        columns: COL_CARD,
    });
}

/** Tab Préstamos — DataTable con préstamos activos del usuario. */
function iniciarTablaPrestamoCard() {
    tablaPresIniciada = true;
    mostrarTab('prestamos');

    $('#prestamos').DataTable({
        language:   idioma,
        processing: true,
        responsive: true,
        ajax: { url: BASE_PC, type: 'get' },
        columns: COL_CARD,
    });
}

/** Tab Clientes — DataTable con clientes del usuario. */
function iniciarTablaClientes() {
    tablaCliIniciada = true;
    mostrarTab('clientes');

    $('#clientecard').DataTable({
        language:   idioma,
        processing: true,
        responsive: true,
        ajax: { url: '/clientes_card', type: 'get' },
        columns: [
            { data: 'datos',       orderable: false, searchable: false, defaultContent: '' },
            { data: 'consecutivo', defaultContent: '' },
        ],
    });
}

/* ════════════════════════════════════════════════════════════════════════════
 * HELPER: rellenar modal de pago con los datos de la cuota
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
