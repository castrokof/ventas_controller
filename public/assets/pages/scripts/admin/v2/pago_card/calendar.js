/**
 * assets/pages/scripts/admin/pagocalender/index.js
 *
 * V2 Pago Card — calendario mensual de cuotas.
 *
 * Endpoints usados:
 *   GET  /admin/v2/pago-card/calendario?mes=M&anio=Y  → conteo por día
 *   GET  /admin/v2/pago-card/dia?fecha=Y-m-d          → cuotas del día
 *   GET  /admin/v2/pago-card/{idd}/edit               → datos cuota pendiente
 *   GET  /admin/v2/pago-card/{idd}/editpay            → datos cuota pagada
 *   POST /admin/v2/pago-card/guardar                  → registrar pago
 *   PUT  /admin/v2/pago-card/{id}                     → actualizar pago
 *   GET  /admin/v2/pago-card/{idp}/editarp?idf=fecha  → datos por préstamo
 *   GET  /admin/v2/prestamo/{idp}/cuotas              → detalle de cuotas
 *   GET  /admin/v2/pago-card/{idp}                    → historial pagos
 *   GET  /admin/v2/pago-card/adelanto?prestamoc_id=X  → cuotas adelantables
 *   GET  /admin/v2/pago-card/atrasos?prestamoc_id=X   → cuotas atrasadas
 */

/* ── Base URL (inyectada desde la vista) ────────────────────────────────────── */
const BASE_PC = (window.CAL_BASE || '/admin/v2/pago-card');

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
};

/* ── Estado del calendario ──────────────────────────────────────────────────── */
const MESES = [
    'Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];
let calYear    = new Date().getFullYear();
let calMonth   = new Date().getMonth() + 1; // 1-12
let calData    = {};   // {fecha: {pagadas, pendientes, atrasadas, monto}}
let selDate    = null; // fecha seleccionada actualmente (string Y-m-d)
const todayStr = new Date().toISOString().slice(0, 10);

/* ── Helpers móvil ──────────────────────────────────────────────────────────── */
function isMobile() { return window.innerWidth <= 768; }

function openPanel() {
    if (!isMobile()) return;
    $('.v2-cal-panel').addClass('panel-open');
    $('#panel-overlay').addClass('active');
    $('body').css('overflow', 'hidden');
}

function closePanel() {
    $('.v2-cal-panel').removeClass('panel-open');
    $('#panel-overlay').removeClass('active');
    $('body').css('overflow', '');
}

/* ── Filtrar cuotas ─────────────────────────────────────────────────────────── */
function filtrarPanel() {
    var q           = ($('#panel-search').val() || '').toLowerCase().trim();
    var filtroEstado = ($('[data-filter].active').data('filter') || 'all');

    $('#btn-clear-search').toggle(q.length > 0);

    var visible = 0;
    $('.cuota-card').each(function () {
        var matchSearch = !q || ($(this).data('search') || '').indexOf(q) >= 0;
        var matchEstado = filtroEstado === 'all'
            || $(this).data('estado') === filtroEstado
            || (filtroEstado === 'P' && $(this).data('estado') === 'T');
        var show = matchSearch && matchEstado;
        $(this).toggle(show);
        if (show) visible++;
    });

    $('#panel-no-results').toggle(visible === 0 && ($('.cuota-card').length > 0));
}

function resetFiltros() {
    $('[data-filter]').removeClass('active');
    $('[data-filter="all"]').addClass('active');
    $('#panel-search').val('');
    $('#btn-clear-search').hide();
    filtrarPanel();
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * DOCUMENT READY
 * ═══════════════════════════════════════════════════════════════════════════════ */
$(function () {

    /* ── Inicializar calendario con el mes actual ─────────────────────────── */
    cargarCalendario(calYear, calMonth);

    /* ── Navegación de mes ────────────────────────────────────────────────── */
    $('#btn-prev-mes').on('click', function () {
        calMonth--;
        if (calMonth < 1) { calMonth = 12; calYear--; }
        cargarCalendario(calYear, calMonth);
    });

    $('#btn-next-mes').on('click', function () {
        calMonth++;
        if (calMonth > 12) { calMonth = 1; calYear++; }
        cargarCalendario(calYear, calMonth);
    });

    $('#btn-hoy').on('click', function () {
        const now = new Date();
        calYear  = now.getFullYear();
        calMonth = now.getMonth() + 1;
        cargarCalendario(calYear, calMonth);
    });

    /* ── Cerrar panel ─────────────────────────────────────────────────────── */
    $('#btn-panel-close').on('click', function () {
        closePanel();
        selDate = null;
        $('.cal-cell').removeClass('selected');
        $('#panel-list').hide().empty();
        $('#panel-placeholder').show();
        $('#panel-title').text('Cobros del día');
        $('#panel-subtitle').text('Selecciona un día del calendario');
        $('#panel-search-wrap').hide();
        $('#panel-filters-wrap').hide();
        resetFiltros();
        $(this).hide();
    });

    /* ── Clic en día del calendario ───────────────────────────────────────── */
    $(document).on('click', '.cal-cell:not(.empty)', function () {
        const fecha = $(this).data('fecha');
        if (!fecha) return;
        $('.cal-cell').removeClass('selected');
        $(this).addClass('selected');
        selDate = fecha;
        cargarPanelDia(fecha);
        openPanel();
    });

    /* ════════════════════════════════════════════════════════════════════════
     * BOTONES EN EL PANEL LATERAL
     * ════════════════════════════════════════════════════════════════════════ */

    /* ── .cal-pay → pagar cuota pendiente/atrasada (por idd) ─────────────── */
    $(document).on('click', '.cal-pay', function () {
        const idd = $(this).data('idd');
        $.get(BASE_PC + '/' + idd + '/edit', function (data) {
            if (!data.result || data.result.length === 0) {
                Swal.fire('Aviso', 'No se encontró la cuota.', 'warning');
                return;
            }
            rellenarModalPago(data.result[0]);
            $('#form-general').data('modo', 'crear').removeData('pid');
            $('.modal-title-pd').text('Registrar Pago — Cuota #' + (data.result[0].d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar la cuota.', 'error'); });
    });

    /* ── .cal-edit → editar pago registrado (por idd) ────────────────────── */
    $(document).on('click', '.cal-edit', function () {
        const idd = $(this).data('idd');
        $.get(BASE_PC + '/' + idd + '/editpay', function (data) {
            if (!data.result) {
                Swal.fire('Aviso', 'No se encontró el pago.', 'warning');
                return;
            }
            rellenarModalPago(data.result);
            $('#form-general').data('modo', 'editar').data('pid', idd);
            $('.modal-title-pd').text('Editar Pago — Cuota #' + (data.result.d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar el pago.', 'error'); });
    });

    /* ── .cal-detalle → cuotas del crédito en modal-d ────────────────────── */
    $(document).on('click', '.cal-detalle', function () {
        const idp = $(this).data('idp');
        $('.modal-title-d').text('Cuotas del Crédito #' + idp);
        const $tbody = $('#detalleCuota tbody');
        $tbody.html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
        $('#modal-d').modal('show');
        $.get('/admin/v2/prestamo/' + idp + '/cuotas', function (data) {
            const est = {
                C: '<span class="badge badge-warning">Pendiente</span>',
                P: '<span class="badge badge-success">Pagada</span>',
                A: '<span class="badge badge-danger">Atrasada</span>',
                T: '<span class="badge badge-info">Cancelada</span>',
            };
            const rows = (data.result || []).map(function (c) {
                return '<tr>'
                    + '<td>' + c.d_numero_cuota + '</td>'
                    + '<td>$' + parseFloat(c.valor_cuota).toLocaleString('es-CO') + '</td>'
                    + '<td>' + c.fecha_cuota + '</td>'
                    + '<td>' + (c.valor_cuota_pagada ? '$' + parseFloat(c.valor_cuota_pagada).toLocaleString('es-CO') : '—') + '</td>'
                    + '<td>' + (est[c.estado] || c.estado) + '</td>'
                    + '</tr>';
            });
            $tbody.html(rows.length
                ? rows.join('')
                : '<tr><td colspan="5" class="text-center text-muted">Sin cuotas.</td></tr>'
            );
        }).fail(function () {
            $tbody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar.</td></tr>');
        });
    });

    /* ── .cal-historial → historial de pagos en modal-dp ─────────────────── */
    $(document).on('click', '.cal-historial', function () {
        const idp = $(this).data('idp');
        $('.modal-title-dp').text('Pagos realizados — Crédito #' + idp);
        $('#detalles').html('<p class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</p>');
        $('#modal-dp').modal('show');
        $.get(BASE_PC + '/' + idp, function (data) {
            const pagos = data.result1 || [];
            if (pagos.length === 0) {
                $('#detalles').html('<p class="text-muted text-center py-3">Sin pagos registrados.</p>');
                return;
            }
            let html = '<table class="table table-sm table-striped table-bordered">'
                     + '<thead class="thead-light"><tr>'
                     + '<th># Cuota</th><th>Abono</th><th>Fecha</th><th>Observación</th>'
                     + '</tr></thead><tbody>';
            pagos.forEach(function (p) {
                html += '<tr>'
                      + '<td>' + p.numero_cuota + '</td>'
                      + '<td>$' + parseFloat(p.valor_abono).toLocaleString('es-CO') + '</td>'
                      + '<td>' + (p.fecha_pago || '—') + '</td>'
                      + '<td>' + (p.observacion_pago || '—') + '</td>'
                      + '</tr>';
            });
            html += '</tbody></table>';
            $('#detalles').html(html);
        }).fail(function () {
            $('#detalles').html('<p class="text-danger">Error al cargar.</p>');
        });
    });

    /* ── Handlers heredados (por si se reutilizan desde otras partes) ───── */

    /* pay: registrar pago por cuota individual (idd) */
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

    /* payp/pagosr: registrar pago por préstamo (idp + fecha) */
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

    /* editpay: editar pago ya registrado */
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

    /* detalle: cuotas del préstamo en modal-d */
    $(document).on('click', '.detalle', function () {
        const id = $(this).attr('id');
        $('.modal-title-d').text('Cuotas del Crédito #' + id);
        const $tbody = $('#detalleCuota tbody');
        $tbody.html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
        $('#modal-d').modal('show');
        $.get('/admin/v2/prestamo/' + id + '/cuotas', function (data) {
            const est = {
                C: '<span class="badge badge-warning">Pendiente</span>',
                P: '<span class="badge badge-success">Pagada</span>',
                A: '<span class="badge badge-danger">Anulada</span>',
                T: '<span class="badge badge-info">Transferida</span>',
            };
            const rows = (data.result || []).map(function (c) {
                return '<tr>'
                    + '<td>' + c.d_numero_cuota + '</td>'
                    + '<td>$' + parseFloat(c.valor_cuota).toLocaleString('es-CO') + '</td>'
                    + '<td>' + c.fecha_cuota + '</td>'
                    + '<td>' + (c.valor_cuota_pagada ? '$' + parseFloat(c.valor_cuota_pagada).toLocaleString('es-CO') : '—') + '</td>'
                    + '<td>' + (est[c.estado] || c.estado) + '</td>'
                    + '</tr>';
            });
            $tbody.html(rows.length
                ? rows.join('')
                : '<tr><td colspan="5" class="text-center text-muted">Sin cuotas.</td></tr>'
            );
        }).fail(function () {
            $tbody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar.</td></tr>');
        });
    });

    /* detallepay: historial de pagos en modal-dp */
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
            let html = '<table class="table table-sm table-striped table-bordered">'
                     + '<thead class="thead-light"><tr>'
                     + '<th># Cuota</th><th>Abono $</th><th>Fecha</th><th>Observación</th>'
                     + '</tr></thead><tbody>';
            pagos.forEach(function (p) {
                html += '<tr>'
                      + '<td>' + p.numero_cuota + '</td>'
                      + '<td>$' + parseFloat(p.valor_abono).toLocaleString('es-CO') + '</td>'
                      + '<td>' + (p.fecha_pago || '—') + '</td>'
                      + '<td>' + (p.observacion_pago || '—') + '</td>'
                      + '</tr>';
            });
            html += '</tbody></table>';
            $('#detalles').html(html);
        }).fail(function () { $('#detalles').html('<p class="text-danger">Error al cargar.</p>'); });
    });

    /* adelantoc: cuotas adelantables en modal-acuotas */
    $(document).on('click', '.adelantoc', function () {
        const id = $(this).attr('id');
        $('.modal-title-acuotas').text('Adelanto de Cuotas — Crédito #' + id);
        if ($.fn.DataTable.isDataTable('#pagoa')) $('#pagoa').DataTable().destroy();
        $('#pagoa').DataTable({
            language: idioma, processing: true,
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

    /* atrasosp: cuotas atrasadas en modal-atrasosp */
    $(document).on('click', '.atrasosp', function () {
        const id = $(this).attr('id');
        $('.modal-title-atrasosp').text('Cuotas Atrasadas — Crédito #' + id);
        if ($.fn.DataTable.isDataTable('#atrasosp')) $('#atrasosp').DataTable().destroy();
        $('#atrasosp').DataTable({
            language: idioma, processing: true,
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

    /* Switch: cambiar fecha de cuota */
    $('#customSwitch1').on('change', function () {
        if ($(this).is(':checked')) {
            $('#chance_fecha').show();
        } else {
            $('#chance_fecha').hide();
            $('#new_date').val('');
        }
    });

    /* Submit: registrar o actualizar pago */
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
                        icon:  esError ? 'warning' : 'success',
                        title: mensajes[resp.success] || 'Operación completada.',
                        showConfirmButton: false,
                        timer: 2200,
                    });

                    if (!esError) {
                        $('#modal-pd').modal('hide');
                        /* Recargar calendario y panel del día actual */
                        cargarCalendario(calYear, calMonth, selDate);
                        if (selDate) cargarPanelDia(selDate);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo registrar el pago.', 'error');
                },
            });
        });
    });

    /* Limpiar modal al cerrar */
    $('#modal-pd').on('hidden.bs.modal', function () {
        $('#form-general')[0].reset();
        $('#form-general').removeData('modo').removeData('pid');
        $('#form_result').html('');
        $('#chance_fecha').hide();
        $('#customSwitch1').prop('checked', false);
    });

    /* Limpiar tabla DataTable al cerrar modales */
    $('#modal-acuotas').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#pagoa')) $('#pagoa').DataTable().destroy();
    });
    $('#modal-atrasosp').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#atrasosp')) $('#atrasosp').DataTable().destroy();
    });

    /* ── Overlay clic (cerrar panel móvil) ───────────────────────────────── */
    $('#panel-overlay').on('click', function () {
        closePanel();
        selDate = null;
        $('.cal-cell').removeClass('selected');
        $('#panel-list').hide().empty();
        $('#panel-placeholder').show();
        $('#panel-title').text('Cobros del día');
        $('#panel-subtitle').text('Selecciona un día del calendario');
        $('#btn-panel-close').hide();
        $('#panel-search-wrap').hide();
        $('#panel-filters-wrap').hide();
        resetFiltros();
    });

    /* ── Buscador ─────────────────────────────────────────────────────────── */
    $(document).on('input', '#panel-search', function () {
        filtrarPanel();
    });
    $(document).on('click', '#btn-clear-search', function () {
        $('#panel-search').val('').trigger('input');
    });

    /* ── Filtros de estado ────────────────────────────────────────────────── */
    $(document).on('click', '[data-filter]', function () {
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        filtrarPanel();
    });

});

/* ═══════════════════════════════════════════════════════════════════════════════
 * CALENDARIO
 * ═══════════════════════════════════════════════════════════════════════════════ */

/**
 * Carga los datos del mes desde el servidor y renderiza el grid.
 * @param {number} year
 * @param {number} month   1-12
 * @param {string} [keepSelected]  fecha que debe quedar seleccionada tras renderizar
 */
function cargarCalendario(year, month, keepSelected) {
    $('#cal-titulo').text(MESES[month - 1] + ' ' + year);
    $('#cal-loading').show();
    $('#cal-grid').hide().empty();

    $.get(BASE_PC + '/calendario', { mes: month, anio: year }, function (data) {
        calData = data.result || {};
        renderizarGrid(year, month, keepSelected);
    }).fail(function () {
        calData = {};
        renderizarGrid(year, month, keepSelected);
    }).always(function () {
        $('#cal-loading').hide();
        $('#cal-grid').show();
    });
}

/**
 * Genera las celdas del grid para el mes dado.
 */
function renderizarGrid(year, month, keepSelected) {
    const $grid = $('#cal-grid').empty();

    /* Offset: primer día del mes (lunes=0 … domingo=6) */
    const primerDia = new Date(year, month - 1, 1).getDay();
    const offset    = primerDia === 0 ? 6 : primerDia - 1;
    const diasMes   = new Date(year, month, 0).getDate();

    /* Celdas vacías al inicio */
    for (let i = 0; i < offset; i++) {
        $grid.append('<div class="cal-cell empty"></div>');
    }

    /* Celdas de días */
    for (let d = 1; d <= diasMes; d++) {
        const fecha = year + '-'
            + String(month).padStart(2, '0') + '-'
            + String(d).padStart(2, '0');

        const info = calData[fecha] || { pagadas: 0, pendientes: 0, atrasadas: 0 };

        let badges = '';
        if (info.pagadas    > 0) badges += '<span class="cb-p">' + info.pagadas    + '</span>';
        if (info.pendientes > 0) badges += '<span class="cb-c">' + info.pendientes + '</span>';
        if (info.atrasadas  > 0) badges += '<span class="cb-a">' + info.atrasadas  + '</span>';

        const classes = ['cal-cell'];
        if (fecha === todayStr)    classes.push('today');
        if (fecha === keepSelected) classes.push('selected');

        $grid.append(
            '<div class="' + classes.join(' ') + '" data-fecha="' + fecha + '">'
          + '  <div class="cal-dn">' + d + '</div>'
          + '  <div class="cal-badges">' + badges + '</div>'
          + '</div>'
        );
    }
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * PANEL LATERAL
 * ═══════════════════════════════════════════════════════════════════════════════ */

/**
 * Carga y renderiza las cuotas del día seleccionado en el panel.
 * @param {string} fecha  Y-m-d
 */
function cargarPanelDia(fecha) {
    const partes = fecha.split('-');
    const label  = parseInt(partes[2], 10) + ' de '
                 + MESES[parseInt(partes[1], 10) - 1]
                 + ' ' + partes[0];

    $('#panel-title').text(label);
    $('#panel-subtitle').html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
    $('#panel-placeholder').hide();
    $('#panel-list').show().html(
        '<div class="text-center py-3 text-muted">'
      + '<i class="fas fa-spinner fa-spin fa-lg"></i></div>'
    );
    $('#btn-panel-close').show();

    $.get(BASE_PC + '/dia', { fecha: fecha }, function (data) {
        const cuotas = data.result || [];

        if (cuotas.length === 0) {
            $('#panel-subtitle').text('Sin cuotas para este día');
            $('#panel-search-wrap').hide();
            $('#panel-filters-wrap').hide();
            $('#panel-list').html(
                '<p class="text-center text-muted py-3" style="font-size:13px">'
              + '<i class="fas fa-check-circle fa-lg d-block mb-1 text-success"></i>'
              + 'No hay cuotas para este día.</p>'
            );
            return;
        }

        const nPag = cuotas.filter(function (c) { return c.estado === 'P' || c.estado === 'T'; }).length;
        const nPen = cuotas.filter(function (c) { return c.estado === 'C'; }).length;
        const nAtr = cuotas.filter(function (c) { return c.estado === 'A'; }).length;

        $('#panel-subtitle').text(
            cuotas.length + ' cuota(s) · '
          + nPag + ' pagada(s) · '
          + nPen + ' pendiente(s) · '
          + nAtr + ' atrasada(s)'
        );

        const estadoBadges = {
            C: '<span class="badge badge-warning">Pendiente</span>',
            P: '<span class="badge badge-success">Pagada</span>',
            A: '<span class="badge badge-danger">Atrasada</span>',
            T: '<span class="badge badge-info">Cancelada</span>',
        };

        let html = '';
        cuotas.forEach(function (c) {
            const esPagada = c.estado === 'P' || c.estado === 'T';
            const badge    = estadoBadges[c.estado] || c.estado;
            const valor    = parseFloat(c.valor_cuota).toLocaleString('es-CO');

            const btnPagar = '<button class="btn btn-xs btn-success cal-pay" data-idd="' + c.idd + '">'
                           + '<i class="fas fa-money-bill-wave"></i> Pagar</button>';
            const btnEditar = '<button class="btn btn-xs btn-outline-primary cal-edit" data-idd="' + c.idd + '">'
                            + '<i class="far fa-edit"></i> Editar</button>';
            const btnDetalle = '<button class="btn btn-xs btn-outline-secondary cal-detalle ml-1" data-idp="' + c.idp + '">'
                             + '<i class="fas fa-list-ul"></i></button>';
            const btnHistorial = '<button class="btn btn-xs btn-outline-info cal-historial ml-1" data-idp="' + c.idp + '">'
                               + '<i class="fas fa-history"></i></button>';

            const alertaAtraso = (c.cuotas_atrasadas > 0)
                ? '<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> '
                  + c.cuotas_atrasadas + ' atraso(s)</small>'
                : '';

            html += '<div class="cuota-card ec-' + c.estado + '"'
                  + ' data-search="' + (escHtml(c.nombres) + ' ' + escHtml(c.apellidos) + ' ' + c.idp + ' ' + c.d_numero_cuota).toLowerCase() + '"'
                  + ' data-estado="' + c.estado + '">'
                  + '  <div class="d-flex align-items-start justify-content-between">'
                  + '    <div style="flex:1;min-width:0">'
                  + '      <div class="cc-name text-truncate">' + escHtml(c.nombres) + ' ' + escHtml(c.apellidos) + '</div>'
                  + '      <div class="cc-meta">Crédito #' + c.idp + ' · Cuota #' + c.d_numero_cuota + '</div>'
                  + '      <div class="cc-meta">' + escHtml(c.tipo_pago || '') + '</div>'
                  + '    </div>'
                  + '    <div class="text-right ml-2" style="flex-shrink:0">'
                  + '      <div class="cc-value">$' + valor + '</div>'
                  + '      ' + badge
                  + '    </div>'
                  + '  </div>'
                  + '  <div class="d-flex justify-content-between align-items-center mt-2">'
                  + '    <div>' + alertaAtraso + '</div>'
                  + '    <div>'
                  + (esPagada ? btnEditar : btnPagar)
                  + btnDetalle + btnHistorial
                  + '    </div>'
                  + '  </div>'
                  + '</div>';
        });

        $('#panel-list').html(html);
        $('#panel-search-wrap').show();
        $('#panel-filters-wrap').show();
        resetFiltros();
    }).fail(function () {
        $('#panel-subtitle').text('Error al cargar');
        $('#panel-list').html(
            '<p class="text-center text-danger py-3">'
          + '<i class="fas fa-exclamation-circle"></i> No se pudieron cargar las cuotas.</p>'
        );
    });
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * HELPERS
 * ═══════════════════════════════════════════════════════════════════════════════ */

/** Rellena el modal de pago con los datos de una cuota. */
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

/** Escapa HTML básico para evitar XSS al insertar datos del servidor en innerHTML. */
function escHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
