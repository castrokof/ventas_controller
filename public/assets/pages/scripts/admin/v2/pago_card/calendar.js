/**
 * assets/pages/scripts/admin/v2/pago_card/calendar.js
 *
 * V2 Pago Card — vista única del cobrador.
 *
 * Endpoints:
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
 *   POST /admin/v2/pago-card/cambiar-fechas           → cambio masivo de fecha
 */

/* ── Base URL (inyectada desde la vista) ────────────────────────────────────── */
const BASE_PC = (window.CAL_BASE || '/admin/v2/pago-card');
const BASE_P  = BASE_PC.replace(/\/pago-card$/, '/prestamo');

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

/* ── Constantes de fechas ───────────────────────────────────────────────────── */
const MESES = [
    'Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];
const DIAS_SEM = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];

let calYear  = new Date().getFullYear();
let calMonth = new Date().getMonth() + 1;
let calData  = {};
let selDate  = null;
const todayStr = new Date().toISOString().slice(0, 10);

/* ── Selección masiva ───────────────────────────────────────────────────────── */
var selMasivo    = false;
var seleccionIds = {};

/* ── Panel préstamos ────────────────────────────────────────────────────────── */
var prstData    = [];
var prstFiltro  = 'all';

/* ── Estado modal cuotas préstamo ───────────────────────────────────────────── */
var mcpIdp      = null;
var mcpNombre   = '';
var mcpCuotas   = [];
var mcpCalYear  = new Date().getFullYear();
var mcpCalMonth = new Date().getMonth() + 1;
var mcpFiltro   = 'all';
var mcpDia      = null;
var mcpSel      = {};

function selBarActualizar() {
    var n = Object.keys(seleccionIds).length;
    if (n === 0) {
        $('#sel-bar').hide();
        $('body').removeClass('sel-masivo-on');
    } else {
        $('#sel-bar').css('display', 'flex');
        $('body').addClass('sel-masivo-on');
    }
    $('#sel-count').text(n);
}

function selLimpiar() {
    Object.keys(seleccionIds).forEach(function (k) { delete seleccionIds[k]; });
    $('.cuota-card').removeClass('seleccionada');
    $('.cuota-check').prop('checked', false);
    selBarActualizar();
}

/* ── Barra de fecha ─────────────────────────────────────────────────────────── */
function fechaBarActualizar(fechaStr) {
    var partes = fechaStr.split('-');
    var y = parseInt(partes[0], 10);
    var m = parseInt(partes[1], 10);
    var d = parseInt(partes[2], 10);
    var diaSem = DIAS_SEM[new Date(y, m - 1, d).getDay()];
    var diaSemCap = diaSem.charAt(0).toUpperCase() + diaSem.slice(1);

    if (fechaStr === todayStr) {
        $('#fecha-label').text('Hoy — ' + d + ' de ' + MESES[m - 1]);
    } else {
        $('#fecha-label').text(d + ' de ' + MESES[m - 1] + ' ' + y);
    }
    $('#fecha-sub').text(diaSemCap);
}

/* ── Filtrar cuotas ─────────────────────────────────────────────────────────── */
function filtrarPanel() {
    var q            = ($('#panel-search').val() || '').toLowerCase().trim();
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

    $('#panel-no-results').toggle(visible === 0 && $('.cuota-card').length > 0);
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

    /* Cargar hoy al abrir ─────────────────────────────────────────────────── */
    selDate = todayStr;
    fechaBarActualizar(todayStr);
    cargarCuotasDia(todayStr);

    /* ── Navegación por día ───────────────────────────────────────────────── */
    $('#btn-prev-dia').on('click', function () {
        var d = new Date(selDate + 'T00:00:00');
        d.setDate(d.getDate() - 1);
        cambiarDia(d.toISOString().slice(0, 10));
    });

    $('#btn-next-dia').on('click', function () {
        var d = new Date(selDate + 'T00:00:00');
        d.setDate(d.getDate() + 1);
        cambiarDia(d.toISOString().slice(0, 10));
    });

    /* ── Ir a hoy ─────────────────────────────────────────────────────────── */
    $('#btn-hoy').on('click', function () {
        var now = new Date();
        calYear  = now.getFullYear();
        calMonth = now.getMonth() + 1;
        cambiarDia(todayStr);
    });

    /* ── Toggle mini calendario ───────────────────────────────────────────── */
    $('#btn-toggle-cal').on('click', function () {
        var visible = $('#cal-container').is(':visible');
        if (visible) {
            $('#cal-container').slideUp(200);
            $('#lbl-toggle-cal').text('Ver calendario');
        } else {
            /* Sincronizar mes con el día seleccionado antes de mostrar */
            var partes = (selDate || todayStr).split('-');
            var y = parseInt(partes[0], 10);
            var m = parseInt(partes[1], 10);
            calYear  = y;
            calMonth = m;
            $('#cal-container').slideDown(200);
            $('#lbl-toggle-cal').text('Ocultar calendario');
            cargarCalendario(calYear, calMonth, selDate);
        }
    });

    /* ── Navegación de mes ────────────────────────────────────────────────── */
    $('#btn-prev-mes').on('click', function () {
        calMonth--;
        if (calMonth < 1) { calMonth = 12; calYear--; }
        cargarCalendario(calYear, calMonth, selDate);
    });

    $('#btn-next-mes').on('click', function () {
        calMonth++;
        if (calMonth > 12) { calMonth = 1; calYear++; }
        cargarCalendario(calYear, calMonth, selDate);
    });

    /* ── Clic en día del calendario ───────────────────────────────────────── */
    $(document).on('click', '.cal-cell:not(.empty)', function () {
        var fecha = $(this).data('fecha');
        if (!fecha) return;
        $('.cal-cell').removeClass('selected');
        $(this).addClass('selected');
        /* Cerrar el calendario al seleccionar día */
        $('#cal-container').slideUp(200);
        $('#lbl-toggle-cal').text('Ver calendario');
        cambiarDia(fecha);
    });

    /* ════════════════════════════════════════════════════════════════════════
     * BOTONES DE CUOTA CARDS
     * ════════════════════════════════════════════════════════════════════════ */

    /* Pagar cuota pendiente/atrasada */
    $(document).on('click', '.cal-pay', function () {
        var idd = $(this).data('idd');
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

    /* Editar pago ya registrado */
    $(document).on('click', '.cal-edit', function () {
        var idd = $(this).data('idd');
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

    /* Detalle cuotas del préstamo */
    $(document).on('click', '.cal-detalle', function () {
        var idp = $(this).data('idp');
        $('.modal-title-d').text('Cuotas del Crédito #' + idp);
        var $tbody = $('#detalleCuota tbody');
        $tbody.html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
        $('#modal-d').modal('show');
        $.get(BASE_P + '/' + idp + '/cuotas', function (data) {
            var est = {
                C: '<span class="badge badge-warning">Pendiente</span>',
                P: '<span class="badge badge-success">Pagada</span>',
                A: '<span class="badge badge-danger">Atrasada</span>',
                T: '<span class="badge badge-success">Saldada (total)</span>',
            };
            var rows = (data.result || []).map(function (c) {
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

    /* Historial de pagos del crédito */
    $(document).on('click', '.cal-historial', function () {
        var idp = $(this).data('idp');
        $('.modal-title-dp').text('Pagos realizados — Crédito #' + idp);
        $('#detalles').html('<p class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</p>');
        $('#modal-dp').modal('show');
        $.get(BASE_PC + '/' + idp, function (data) {
            var pagos = data.result1 || [];
            if (pagos.length === 0) {
                $('#detalles').html('<p class="text-muted text-center py-3">Sin pagos registrados.</p>');
                return;
            }
            var html = '<table class="table table-sm table-striped table-bordered">'
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

    /* ── Handlers heredados ───────────────────────────────────────────────── */

    $(document).on('click', '.pay', function () {
        var id = $(this).attr('id') || $(this).data('id');
        $.get(BASE_PC + '/' + id + '/edit', function (data) {
            if (!data.result || data.result.length === 0) return;
            rellenarModalPago(data.result[0]);
            $('#form-general').data('modo', 'crear').removeData('pid');
            $('.modal-title-pd').text('Registrar Pago — Cuota #' + (data.result[0].d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar la cuota.', 'error'); });
    });

    $(document).on('click', '.payp, .pagosr', function () {
        var id  = $(this).attr('id');
        var idf = $(this).attr('idf');
        $.get(BASE_PC + '/' + id + '/editarp', { idf: idf }, function (data) {
            if (!data.result || data.result.length === 0) return;
            rellenarModalPago(data.result[0]);
            $('#form-general').data('modo', 'crear').removeData('pid');
            $('.modal-title-pd').text('Registrar Pago — Crédito #' + id);
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar el préstamo.', 'error'); });
    });

    $(document).on('click', '.editpay', function () {
        var id = $(this).attr('id') || $(this).data('id');
        $.get(BASE_PC + '/' + id + '/editpay', function (data) {
            if (!data.result) return;
            rellenarModalPago(data.result);
            $('#form-general').data('modo', 'editar').data('pid', id);
            $('.modal-title-pd').text('Editar Pago — Cuota #' + (data.result.d_numero_cuota || ''));
            $('#modal-pd').modal('show');
        }).fail(function () { Swal.fire('Error', 'No se pudo cargar el pago.', 'error'); });
    });

    $(document).on('click', '.detalle', function () {
        var id = $(this).attr('id');
        $('.modal-title-d').text('Cuotas del Crédito #' + id);
        var $tbody = $('#detalleCuota tbody');
        $tbody.html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
        $('#modal-d').modal('show');
        $.get(BASE_P + '/' + id + '/cuotas', function (data) {
            var est = {
                C: '<span class="badge badge-warning">Pendiente</span>',
                P: '<span class="badge badge-success">Pagada</span>',
                A: '<span class="badge badge-danger">Anulada</span>',
                T: '<span class="badge badge-info">Transferida</span>',
            };
            var rows = (data.result || []).map(function (c) {
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

    $(document).on('click', '.detallepay', function () {
        var id = $(this).attr('id');
        $('.modal-title-dp').text('Pagos realizados — Crédito #' + id);
        $('#detalles').html('<p class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</p>');
        $('#modal-dp').modal('show');
        $.get(BASE_PC + '/' + id, function (data) {
            var pagos = data.result1 || [];
            if (pagos.length === 0) {
                $('#detalles').html('<p class="text-muted text-center py-3">Sin pagos registrados.</p>');
                return;
            }
            var html = '<table class="table table-sm table-striped table-bordered">'
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

    /* Cuotas adelantables */
    $(document).on('click', '.adelantoc', function () {
        var id = $(this).attr('id');
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

    /* Cuotas atrasadas */
    $(document).on('click', '.atrasosp', function () {
        var id = $(this).attr('id');
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

    $('#customSwitch1').on('change', function () {
        if ($(this).is(':checked')) {
            $('#chance_fecha').show();
        } else {
            $('#chance_fecha').hide();
            $('#new_date').val('');
        }
    });

    $('#form-general').on('submit', function (e) {
        e.preventDefault();

        var modo = $(this).data('modo') || 'crear';
        var pid  = $(this).data('pid');
        var url  = (modo === 'editar') ? BASE_PC + '/' + pid : BASE_PC + '/guardar';

        Swal.fire({
            title: modo === 'editar' ? '¿Actualizar pago?' : '¿Registrar pago?',
            icon: 'question',
            showCancelButton:  true,
            confirmButtonText: 'Sí',
            cancelButtonText:  'Cancelar',
        }).then(function (res) {
            if (!res.value) return;

            var data = $(e.target).serialize()
                     + (modo === 'editar' ? '&_method=PUT' : '');

            $.ajax({
                url:      url,
                method:   'POST',
                data:     data,
                dataType: 'json',
                success: function (resp) {
                    var mensajes = {
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
                    var errores = ['error','noadelanto','noa','adelantos','vcda','adelantosa','okcaerror'];
                    var esError = errores.includes(resp.success);

                    Swal.fire({
                        icon:  esError ? 'warning' : 'success',
                        title: mensajes[resp.success] || 'Operación completada.',
                        showConfirmButton: false,
                        timer: 2200,
                    });

                    if (!esError) {
                        $('#modal-pd').modal('hide');
                        if (selDate) cargarCuotasDia(selDate);
                        if ($('#cal-container').is(':visible')) {
                            cargarCalendario(calYear, calMonth, selDate);
                        }
                        if ($('#prestamos-container').is(':visible')) {
                            cargarListaPrestamos();
                        }
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo registrar el pago.', 'error');
                },
            });
        });
    });

    $('#modal-pd').on('hidden.bs.modal', function () {
        $('#form-general')[0].reset();
        $('#form-general').removeData('modo').removeData('pid');
        $('#form_result').html('');
        $('#chance_fecha').hide();
        $('#customSwitch1').prop('checked', false);
    });

    $('#modal-acuotas').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#pagoa')) $('#pagoa').DataTable().destroy();
    });
    $('#modal-atrasosp').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#atrasosp')) $('#atrasosp').DataTable().destroy();
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

    /* ════════════════════════════════════════════════════════════════════════
     * SELECCIÓN MASIVA
     * ════════════════════════════════════════════════════════════════════════ */

    $('#btn-modo-masivo').on('click', function () {
        selMasivo = !selMasivo;
        $(this).toggleClass('activo', selMasivo);
        if (!selMasivo) { selLimpiar(); }
        if (selDate) cargarCuotasDia(selDate);
    });

    $(document).on('change', '.cuota-check', function () {
        var idd   = $(this).data('idd');
        var $card = $(this).closest('.cuota-card');
        if ($(this).is(':checked')) {
            seleccionIds[idd] = {
                nombre:      $card.data('nombre') || '',
                cuota:       $card.data('cuota')  || '',
                fechaActual: $card.data('fecha')  || ''
            };
            $card.addClass('seleccionada');
        } else {
            delete seleccionIds[idd];
            $card.removeClass('seleccionada');
        }
        selBarActualizar();
    });

    $('#btn-sel-limpiar').on('click', function () {
        selLimpiar();
    });

    $('#btn-sel-cambiar').on('click', function () {
        var n = Object.keys(seleccionIds).length;
        if (n === 0) return;
        $('#cf-count').text(n);
        $('#cf-nueva-fecha').val('');
        $('#cf-feedback').hide().text('');
        $('#modal-cambiar-fecha').modal('show');
    });

    $('#btn-cf-confirmar').on('click', function () {
        var nuevaFecha = $('#cf-nueva-fecha').val();
        if (!nuevaFecha) {
            $('#cf-feedback').text('Debes seleccionar una fecha.').show();
            return;
        }
        var ids  = Object.keys(seleccionIds).map(Number);
        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...');

        $.ajax({
            url:      BASE_PC + '/cambiar-fechas',
            method:   'POST',
            dataType: 'json',
            data: {
                _token:      $('meta[name="csrf-token"]').attr('content')
                          || $('input[name="_token"]').first().val(),
                ids:         ids,
                nueva_fecha: nuevaFecha
            },
            success: function (resp) {
                if (resp.success) {
                    $('#modal-cambiar-fecha').modal('hide');
                    selLimpiar();
                    selMasivo = false;
                    $('#btn-modo-masivo').removeClass('activo');
                    if (selDate) cargarCuotasDia(selDate);
                    if ($('#cal-container').is(':visible')) {
                        cargarCalendario(calYear, calMonth, selDate);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: resp.actualizadas + ' cuota(s) actualizadas',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    $('#cf-feedback').text(resp.msg || 'Error al actualizar.').show();
                    $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Aplicar cambio');
                }
            },
            error: function () {
                $('#cf-feedback').text('Error de red. Intenta de nuevo.').show();
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Aplicar cambio');
            }
        });
    });

    $('#modal-cambiar-fecha').on('hidden.bs.modal', function () {
        $('#btn-cf-confirmar').prop('disabled', false)
            .html('<i class="fas fa-check mr-1"></i>Aplicar cambio');
        $('#cf-feedback').hide().text('');
    });

    /* ════════════════════════════════════════════════════════════════════════
     * PANEL PRÉSTAMOS
     * ════════════════════════════════════════════════════════════════════════ */

    $('#btn-toggle-prestamos').on('click', function () {
        var visible = $('#prestamos-container').is(':visible');
        if (visible) {
            $('#prestamos-container').slideUp(200);
            $(this).removeClass('activo');
        } else {
            $('#prestamos-container').slideDown(200);
            $(this).addClass('activo');
            if (!prstData.length) cargarListaPrestamos();
        }
    });

    $('#btn-prst-refresh').on('click', function (e) {
        e.stopPropagation();
        cargarListaPrestamos();
    });

    $(document).on('click', '[data-prst-filter]', function () {
        $('[data-prst-filter]').removeClass('active');
        $(this).addClass('active');
        prstFiltro = $(this).data('prst-filter');
        renderListaPrestamos();
    });

    $(document).on('click', '.prst-card', function () {
        var idp    = $(this).data('idp');
        var nombre = $(this).data('nombre');
        var filtro = $(this).data('filtro');
        abrirCuotasPrestamo(idp, nombre, false, filtro);
    });

    /* ════════════════════════════════════════════════════════════════════════
     * MODAL CUOTAS DEL PRÉSTAMO (calendario + adelantos)
     * ════════════════════════════════════════════════════════════════════════ */

    /* Abrir al clicar nombre / crédito en la cuota card */
    $(document).on('click', '.cc-info-link', function (e) {
        if (selMasivo) return; /* no abrir en modo masivo */
        var idp    = $(this).data('idp');
        var nombre = $(this).data('nombre');
        abrirCuotasPrestamo(idp, nombre);
    });

    /* Navegación de mes en el mini-calendario del modal */
    $(document).on('click', '#mcp-prev-mes', function () {
        mcpCalMonth--;
        if (mcpCalMonth < 1) { mcpCalMonth = 12; mcpCalYear--; }
        mcpRenderCalendario();
    });
    $(document).on('click', '#mcp-next-mes', function () {
        mcpCalMonth++;
        if (mcpCalMonth > 12) { mcpCalMonth = 1; mcpCalYear++; }
        mcpRenderCalendario();
    });

    /* Clic en día del mini-calendario del modal */
    $(document).on('click', '#mcp-cal-grid .cal-cell:not(.empty)', function () {
        var f = $(this).data('fecha');
        if (!f) return;
        mcpDia = f;
        $('#mcp-cal-grid .cal-cell').removeClass('selected');
        $(this).addClass('selected');
        var partes = f.split('-');
        $('#mcp-dia-lbl span').text(parseInt(partes[2], 10) + ' de ' + MESES[parseInt(partes[1], 10) - 1] + ' ' + partes[0]);
        $('#mcp-dia-lbl').show();
        mcpRenderLista();
    });

    /* Quitar filtro de día */
    $(document).on('click', '#mcp-limpiar-dia', function (e) {
        e.preventDefault();
        mcpDia = null;
        $('#mcp-cal-grid .cal-cell').removeClass('selected');
        $('#mcp-dia-lbl').hide();
        mcpRenderLista();
    });

    /* Filtros de estado en modal */
    $(document).on('click', '[data-mcp-filter]', function () {
        $('[data-mcp-filter]').removeClass('active');
        $(this).addClass('active');
        mcpFiltro = $(this).data('mcp-filter');
        mcpRenderLista();
    });

    /* Checkbox cuota futura en modal */
    $(document).on('change', '.mcp-check', function () {
        var idd   = $(this).data('idd');
        var cuota = mcpCuotas.filter(function (c) { return c.idd == idd; })[0];
        if (!cuota) return;
        if ($(this).is(':checked')) {
            mcpSel[idd] = cuota;
        } else {
            delete mcpSel[idd];
        }
        $(this).closest('.mcp-row').toggleClass('mcp-sel', $(this).is(':checked'));
        mcpActualizarFooter();
    });

    /* Botón pagar seleccionadas */
    $(document).on('click', '#btn-mcp-pagar', function () {
        var n = Object.keys(mcpSel).length;
        if (!n) return;
        Swal.fire({
            title: '¿Pagar ' + n + ' cuota(s)?',
            html: 'Se registrará el valor completo de cada cuota como adelanto.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, pagar',
            cancelButtonText: 'Cancelar',
        }).then(function (res) {
            if (res.value) mcpPagarSeleccionadas();
        });
    });

    /* Limpiar modal al cerrar */
    $('#modal-cuotas-prestamo').on('hidden.bs.modal', function () {
        mcpSel = {};
        mcpDia = null;
        mcpFiltro = 'all';
        $('#mcp-progreso').hide().html('');
        $('#mcp-dia-lbl').hide();
        if ($('#prestamos-container').is(':visible')) cargarListaPrestamos();
    });

});

/* ═══════════════════════════════════════════════════════════════════════════════
 * CAMBIAR DÍA
 * ═══════════════════════════════════════════════════════════════════════════════ */

function cambiarDia(fechaStr) {
    selDate = fechaStr;
    fechaBarActualizar(fechaStr);

    /* Sincronizar mes del mini calendario */
    var partes = fechaStr.split('-');
    var y = parseInt(partes[0], 10);
    var m = parseInt(partes[1], 10);

    if (y !== calYear || m !== calMonth) {
        calYear  = y;
        calMonth = m;
        if ($('#cal-container').is(':visible')) {
            cargarCalendario(calYear, calMonth, fechaStr);
        }
    } else if ($('#cal-container').is(':visible')) {
        $('.cal-cell').removeClass('selected');
        $('.cal-cell[data-fecha="' + fechaStr + '"]').addClass('selected');
    }

    cargarCuotasDia(fechaStr);
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * CUOTAS DEL DÍA
 * ═══════════════════════════════════════════════════════════════════════════════ */

function cargarCuotasDia(fecha) {
    $('#cuotas-loading').show();
    $('#panel-list').empty();
    $('#panel-empty').hide();
    $('#panel-no-results').hide();
    $('#resumen-bar').hide();

    $.ajax({
        url:      BASE_PC + '/dia',
        method:   'GET',
        data:     { fecha: fecha },
        dataType: 'json',
        success: function (data) {
            try {
                var cuotas = ((data || {}).result) || [];

                var nPen  = cuotas.filter(function (c) { return c.estado === 'C'; }).length;
                var nAtr  = cuotas.filter(function (c) { return c.estado === 'A'; }).length;
                var nPag  = cuotas.filter(function (c) { return c.estado === 'P' || c.estado === 'T'; }).length;
                var monto = cuotas
                    .filter(function (c) { return c.estado === 'C' || c.estado === 'A'; })
                    .reduce(function (acc, c) { return acc + parseFloat(c.valor_cuota || 0); }, 0);

                $('#res-pend').text(nPen);
                $('#res-atra').text(nAtr);
                $('#res-pago').text(nPag);
                $('#res-monto').text('$' + monto.toLocaleString('es-CO'));
                $('#resumen-bar').show();

                if (cuotas.length === 0) {
                    $('#panel-empty').show();
                    return;
                }

                var estadoBadges = {
                    C: '<span class="badge badge-warning">Pendiente</span>',
                    P: '<span class="badge badge-success">Pagada</span>',
                    A: '<span class="badge badge-danger">Atrasada</span>',
                    T: '<span class="badge badge-success">Saldada (total)</span>',
                };

                var html = '';
                cuotas.forEach(function (c) {
                    var esPagada = c.estado === 'P' || c.estado === 'T';
                    var badge    = estadoBadges[c.estado] || c.estado;
                    var valor    = parseFloat(c.valor_cuota || 0).toLocaleString('es-CO');

                    var btnPagar = '<button class="btn btn-xs btn-success cal-pay" data-idd="' + c.idd + '">'
                                 + '<i class="fas fa-money-bill-wave"></i> Pagar</button>';
                    var btnEditar = '<button class="btn btn-xs btn-outline-primary cal-edit" data-idd="' + c.idd + '">'
                                  + '<i class="far fa-edit"></i> Editar</button>';
                    var btnDetalle = '<button class="btn btn-xs btn-outline-secondary cal-detalle ml-1" data-idp="' + c.idp + '">'
                                   + '<i class="fas fa-list-ul"></i></button>';
                    var btnHistorial = '<button class="btn btn-xs btn-outline-info cal-historial ml-1" data-idp="' + c.idp + '">'
                                     + '<i class="fas fa-history"></i></button>';

                    var alertaAtraso = (c.cuotas_atrasadas > 0)
                        ? '<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> '
                          + c.cuotas_atrasadas + ' atraso(s)</small>'
                        : '';

                    var yaSel     = selMasivo && !!seleccionIds[c.idd];
                    var checkHtml = selMasivo && !esPagada
                        ? '<input type="checkbox" class="cuota-check" data-idd="' + c.idd + '"'
                          + (yaSel ? ' checked' : '') + '>'
                        : '';

                    html += '<div class="cuota-card ec-' + c.estado + (yaSel ? ' seleccionada' : '') + '"'
                          + ' data-search="' + (escHtml(c.nombres) + ' ' + escHtml(c.apellidos) + ' ' + c.idp + ' ' + c.d_numero_cuota).toLowerCase() + '"'
                          + ' data-estado="' + c.estado + '"'
                          + ' data-nombre="' + escHtml(c.nombres + ' ' + c.apellidos) + '"'
                          + ' data-cuota="' + c.d_numero_cuota + '"'
                          + ' data-fecha="' + escHtml(c.fecha_cuota || '') + '">'
                          + '  <div class="d-flex align-items-start justify-content-between">'
                          + '    ' + checkHtml
                          + '    <div style="flex:1;min-width:0">'
                          + '      <div class="cc-info-link" data-idp="' + c.idp + '" data-nombre="' + escHtml(c.nombres + ' ' + c.apellidos) + '">'
                          + '        <div class="cc-name text-truncate">' + escHtml(c.nombres) + ' ' + escHtml(c.apellidos) + '</div>'
                          + '        <div class="cc-meta">Crédito #' + c.idp + ' · Cuota #' + c.d_numero_cuota + '</div>'
                          + '      </div>'
                          + '      <div class="cc-meta">' + escHtml(c.tipo_pago || '') + '</div>'
                          + '    </div>'
                          + '    <div class="text-right ml-2" style="flex-shrink:0">'
                          + '      <div class="cc-value">$' + valor + '</div>'
                          + '      ' + badge
                          + '    </div>'
                          + '  </div>'
                          + (selMasivo ? '' :
                               '  <div class="d-flex justify-content-between align-items-center mt-2">'
                             + '    <div>' + alertaAtraso + '</div>'
                             + '    <div>'
                             + (esPagada ? btnEditar : btnPagar)
                             + btnDetalle + btnHistorial
                             + '    </div>'
                             + '  </div>'
                            )
                          + '</div>';
                });

                $('#panel-list').html(html);
                resetFiltros();

            } catch (e) {
                $('#panel-list').html(
                    '<p class="text-center text-danger py-3">'
                  + '<i class="fas fa-exclamation-circle"></i> Error al procesar los datos.</p>'
                );
            }
        },
        error: function () {
            $('#panel-list').html(
                '<p class="text-center text-danger py-3">'
              + '<i class="fas fa-exclamation-circle"></i> No se pudieron cargar las cuotas.</p>'
            );
        },
        complete: function () {
            $('#cuotas-loading').hide();
        }
    });
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * CALENDARIO (mini, colapsable)
 * ═══════════════════════════════════════════════════════════════════════════════ */

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

function renderizarGrid(year, month, keepSelected) {
    var $grid = $('#cal-grid').empty();

    var primerDia = new Date(year, month - 1, 1).getDay();
    var offset    = primerDia === 0 ? 6 : primerDia - 1;
    var diasMes   = new Date(year, month, 0).getDate();

    for (var i = 0; i < offset; i++) {
        $grid.append('<div class="cal-cell empty"></div>');
    }

    for (var d = 1; d <= diasMes; d++) {
        var fecha = year + '-'
            + String(month).padStart(2, '0') + '-'
            + String(d).padStart(2, '0');

        var info = calData[fecha] || { pagadas: 0, pendientes: 0, atrasadas: 0 };

        var badges = '';
        if (info.pagadas    > 0) badges += '<span class="cb-p">' + info.pagadas    + '</span>';
        if (info.pendientes > 0) badges += '<span class="cb-c">' + info.pendientes + '</span>';
        if (info.atrasadas  > 0) badges += '<span class="cb-a">' + info.atrasadas  + '</span>';

        var classes = ['cal-cell'];
        if (fecha === todayStr)     classes.push('today');
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
 * HELPERS
 * ═══════════════════════════════════════════════════════════════════════════════ */

function rellenarModalPago(d) {
    $('#nombres').val((d.nombres || '') + ' ' + (d.apellidos || ''));
    $('#tipo_pago').val(d.tipo_pago || '');
    $('#idp').val(d.idp || d.prestamo_id || '');
    $('#fecha_cuota').val(d.fecha_cuota || '');
    $('#n_cuota').val(d.d_numero_cuota || '');
    $('#valor_cuota').val(d.valor_cuota || '');
    $('#estado_cuota').val(d.estado || '');
    $('#vatraso').val(d.monto_atrasado || 0);
    $('#valor_abono').val(d.valor_cuota || '');
    $('#observacion').val('');
}

function escHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * MODAL CUOTAS DEL PRÉSTAMO
 * ═══════════════════════════════════════════════════════════════════════════════ */

function abrirCuotasPrestamo(idp, nombre, reload, filtroInicial) {
    mcpIdp    = idp;
    mcpNombre = nombre || ('Crédito #' + idp);
    if (!reload) {
        var fi      = filtroInicial || 'all';
        mcpCuotas   = [];
        mcpSel      = {};
        mcpDia      = null;
        mcpFiltro   = fi;
        mcpCalYear  = new Date().getFullYear();
        mcpCalMonth = new Date().getMonth() + 1;
        $('[data-mcp-filter]').removeClass('active');
        $('[data-mcp-filter="' + fi + '"]').addClass('active');
    }

    $('#mcp-titulo').text('Crédito #' + idp + ' — ' + nombre);
    $('#mcp-subtitulo').text('Cargando...');
    $('#mcp-loading').show().html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
    $('#mcp-content').hide();
    $('#mcp-footer').hide();
    $('#mcp-progreso').hide().html('');
    $('#mcp-dia-lbl').hide();

    if (!reload) {
        $('#modal-cuotas-prestamo').modal('show');
    }

    $.ajax({
        url:      BASE_PC + '/cuotas/' + idp,
        method:   'GET',
        dataType: 'json',
        success: function (data) {
            mcpCuotas = ((data || {}).result) || [];

            /* Ir al mes de la primera cuota C futura */
            if (!reload) {
                var futura = null;
                for (var i = 0; i < mcpCuotas.length; i++) {
                    var c = mcpCuotas[i];
                    if (c.estado === 'C' && c.fecha_cuota > todayStr) { futura = c; break; }
                }
                if (futura) {
                    var p = futura.fecha_cuota.split('-');
                    mcpCalYear  = parseInt(p[0], 10);
                    mcpCalMonth = parseInt(p[1], 10);
                }
            }

            var nPend = mcpCuotas.filter(function (c) { return c.estado === 'C'; }).length;
            var nAtr  = mcpCuotas.filter(function (c) { return c.estado === 'A'; }).length;
            $('#mcp-subtitulo').text(
                mcpCuotas.length + ' cuotas · ' + nPend + ' pendientes · ' + nAtr + ' atrasadas'
            );

            mcpRenderCalendario();
            mcpRenderLista();
            mcpActualizarFooter();
            $('#mcp-loading').hide();
            $('#mcp-content').show();
            $('#mcp-footer').css('display', 'flex');
        },
        error: function () {
            $('#mcp-loading').html(
                '<p class="text-center text-danger py-3">'
              + '<i class="fas fa-exclamation-circle"></i> Error al cargar las cuotas.</p>'
            );
        }
    });
}

function mcpRenderCalendario() {
    var year  = mcpCalYear;
    var month = mcpCalMonth;
    $('#mcp-cal-titulo').text(MESES[month - 1] + ' ' + year);

    /* Agrupar cuotas por fecha */
    var porFecha = {};
    mcpCuotas.forEach(function (c) {
        if (!porFecha[c.fecha_cuota]) {
            porFecha[c.fecha_cuota] = { C: 0, A: 0, P: 0, T: 0 };
        }
        porFecha[c.fecha_cuota][c.estado] = (porFecha[c.fecha_cuota][c.estado] || 0) + 1;
    });

    var $grid     = $('#mcp-cal-grid').empty();
    var primerDia = new Date(year, month - 1, 1).getDay();
    var offset    = primerDia === 0 ? 6 : primerDia - 1;
    var diasMes   = new Date(year, month, 0).getDate();

    for (var i = 0; i < offset; i++) {
        $grid.append('<div class="cal-cell empty"></div>');
    }

    for (var d = 1; d <= diasMes; d++) {
        var fecha = year + '-'
            + String(month).padStart(2, '0') + '-'
            + String(d).padStart(2, '0');

        var info   = porFecha[fecha] || {};
        var badges = '';
        if (info.P > 0 || info.T > 0) badges += '<span class="cb-p">' + ((info.P || 0) + (info.T || 0)) + '</span>';
        if (info.C > 0)               badges += '<span class="cb-c">' + info.C + '</span>';
        if (info.A > 0)               badges += '<span class="cb-a">' + info.A + '</span>';

        var classes = ['cal-cell'];
        if (!badges)          classes.push('cal-cell-dim');
        if (fecha === todayStr) classes.push('today');
        if (fecha === mcpDia)   classes.push('selected');

        $grid.append(
            '<div class="' + classes.join(' ') + '" data-fecha="' + fecha + '">'
          + '  <div class="cal-dn">' + d + '</div>'
          + '  <div class="cal-badges">' + badges + '</div>'
          + '</div>'
        );
    }
}

function mcpRenderLista() {
    var filtradas = mcpCuotas.filter(function (c) {
        var matchEstado = mcpFiltro === 'all'
            || c.estado === mcpFiltro
            || (mcpFiltro === 'P' && c.estado === 'T');
        var matchDia = !mcpDia || c.fecha_cuota === mcpDia;
        return matchEstado && matchDia;
    });

    if (!filtradas.length) {
        $('#mcp-lista').html(
            '<p class="text-center text-muted py-3" style="font-size:12px">'
          + '<i class="fas fa-search mr-1"></i>Sin resultados.</p>'
        );
        return;
    }

    var estadoBadges = {
        C: '<span class="badge badge-warning">Pendiente</span>',
        P: '<span class="badge badge-success">Pagada</span>',
        A: '<span class="badge badge-danger">Atrasada</span>',
        T: '<span class="badge badge-success">Saldada (total)</span>',
    };

    var html = '';
    filtradas.forEach(function (c) {
        var esPagada  = c.estado === 'P' || c.estado === 'T';
        var esFuturaC = c.estado === 'C' && c.fecha_cuota > todayStr;
        var badge     = estadoBadges[c.estado] || c.estado;
        var valor     = parseFloat(c.valor_cuota || 0).toLocaleString('es-CO');
        var yaSel     = !!mcpSel[c.idd];

        /* Fecha corta: "1 Jun" */
        var fp   = c.fecha_cuota.split('-');
        var fLbl = parseInt(fp[2], 10) + ' ' + MESES[parseInt(fp[1], 10) - 1].slice(0, 3);

        var checkHtml = esFuturaC
            ? '<input type="checkbox" class="mcp-check" data-idd="' + c.idd + '"' + (yaSel ? ' checked' : '') + '>'
            : '<span style="width:16px;display:inline-block;flex-shrink:0"></span>';

        var acciones = '';
        if (!esPagada) {
            acciones = '<button class="btn btn-xs btn-success cal-pay ml-1" data-idd="' + c.idd + '" title="Pagar cuota">'
                     + '<i class="fas fa-money-bill-wave"></i></button>';
        }

        html += '<div class="mcp-row ec-' + c.estado + (yaSel ? ' mcp-sel' : '') + '">'
              + checkHtml
              + '<span class="mcp-date">' + fLbl + '</span>'
              + '<span class="mcp-num">#' + c.d_numero_cuota + '</span>'
              + badge
              + '<span class="mcp-val">$' + valor + '</span>'
              + acciones
              + '</div>';
    });

    $('#mcp-lista').html(html);
}

function mcpActualizarFooter() {
    var lista = Object.values(mcpSel);
    var n     = lista.length;
    var monto = lista.reduce(function (a, c) { return a + parseFloat(c.valor_cuota || 0); }, 0);

    if (n === 0) {
        $('#mcp-sel-info').text('Toca el checkbox de una cuota futura para seleccionar');
        $('#btn-mcp-pagar').prop('disabled', true);
    } else {
        $('#mcp-sel-info').text(n + ' cuota(s) · $' + monto.toLocaleString('es-CO'));
        $('#btn-mcp-pagar').prop('disabled', false);
    }
}

function mcpPagarSeleccionadas() {
    var lista = Object.values(mcpSel);
    if (!lista.length) return;

    /* Ordenar por número de cuota */
    lista.sort(function (a, b) { return a.d_numero_cuota - b.d_numero_cuota; });

    $('#btn-mcp-pagar').prop('disabled', true);
    $('#mcp-progreso').show();

    var idx  = 0;
    var ok   = 0;
    var fail = 0;
    var csrf = $('meta[name="csrf-token"]').attr('content')
            || $('input[name="_token"]').first().val();

    function siguiente() {
        if (idx >= lista.length) {
            var msg = '<i class="fas fa-check-circle text-success mr-1"></i>'
                    + ok + ' cuota(s) pagada(s)';
            if (fail) msg += ' · <span class="text-danger">' + fail + ' con error</span>';
            $('#mcp-progreso').html(msg);
            mcpSel = {};
            mcpActualizarFooter();
            /* Recargar modal y lista principal */
            setTimeout(function () {
                abrirCuotasPrestamo(mcpIdp, mcpNombre, true);
            }, 1200);
            if (selDate) cargarCuotasDia(selDate);
            if ($('#cal-container').is(':visible')) {
                cargarCalendario(calYear, calMonth, selDate);
            }
            if ($('#prestamos-container').is(':visible')) {
                cargarListaPrestamos();
            }
            return;
        }

        var c = lista[idx++];
        $('#mcp-progreso').html(
            '<i class="fas fa-spinner fa-spin mr-1"></i>Pagando cuota #' + c.d_numero_cuota
          + ' (' + idx + '/' + lista.length + ')...'
        );

        $.ajax({
            url:      BASE_PC + '/guardar',
            method:   'POST',
            dataType: 'json',
            data: {
                _token:       csrf,
                prestamo_id:  c.idp,
                numero_cuota: c.d_numero_cuota,
                valor_cuota:  c.valor_cuota,
                valor_abono:  c.valor_cuota,
                estado_cuota: 'C',
                fecha_pago:   c.fecha_cuota,
                vatraso:      0,
                observacion_pago: ''
            },
            success: function (resp) {
                var errores = ['error','noadelanto','noa','adelantos','vcda','adelantosa','okcaerror'];
                if (errores.includes(resp.success)) fail++;
                else ok++;
                siguiente();
            },
            error: function () {
                fail++;
                siguiente();
            }
        });
    }

    siguiente();
}

/* ═══════════════════════════════════════════════════════════════════════════════
 * PANEL DE PRÉSTAMOS
 * ═══════════════════════════════════════════════════════════════════════════════ */

function cargarListaPrestamos() {
    $('#prst-loading').show();
    $('#prst-list').empty();
    $('#prst-empty').hide();
    prstData = [];

    $.ajax({
        url:      BASE_PC + '/prestamos',
        method:   'GET',
        dataType: 'json',
        success: function (data) {
            prstData = ((data || {}).result) || [];
            renderListaPrestamos();
        },
        error: function () {
            $('#prst-list').html(
                '<p class="text-danger text-center py-2" style="font-size:12px">'
              + '<i class="fas fa-exclamation-circle mr-1"></i>Error al cargar préstamos.</p>'
            );
        },
        complete: function () {
            $('#prst-loading').hide();
        }
    });
}

function renderListaPrestamos() {
    var msgs = {
        all:    'No hay préstamos activos.',
        atraso: 'Sin préstamos con atrasos.',
        hoy:    'Sin préstamos con cobro hoy.'
    };

    var lista;
    if (prstFiltro === 'atraso') {
        lista = prstData.filter(function (p) { return p.cuotas_atrasadas > 0; });
    } else if (prstFiltro === 'hoy') {
        lista = prstData.filter(function (p) { return p.cuotas_hoy > 0; });
    } else {
        lista = prstData;
    }

    if (!lista.length) {
        $('#prst-empty').text(msgs[prstFiltro] || msgs.all).show();
        $('#prst-list').empty();
        return;
    }
    $('#prst-empty').hide();

    var html = '';
    lista.forEach(function (p) {
        var tieneAtraso = p.cuotas_atrasadas > 0;
        var tieneHoy    = p.cuotas_hoy > 0;
        var nombre      = escHtml(p.nombres + ' ' + p.apellidos);
        var pendiente   = parseFloat(p.monto_pendiente || 0).toLocaleString('es-CO');
        var atrasado    = parseFloat(p.monto_atrasado  || 0).toLocaleString('es-CO');

        /* Al tocar: si tiene atrasos abre filtro A; si tiene cuota hoy abre C; si no, all */
        var filtroModal = tieneAtraso ? 'A' : (tieneHoy ? 'C' : 'all');

        html += '<div class="prst-card ' + (tieneAtraso ? 'has-atraso' : 'no-atraso') + '"'
              + ' data-idp="' + p.idp + '" data-nombre="' + nombre + '"'
              + ' data-filtro="' + filtroModal + '">'
              + '  <div class="d-flex align-items-start justify-content-between">'
              + '    <div style="flex:1;min-width:0">'
              + '      <div class="prst-name text-truncate">'
              + (tieneHoy ? '<span class="badge badge-primary mr-1" style="font-size:9px">HOY</span>' : '')
              + nombre + '</div>'
              + '      <div class="prst-meta">'
              +          escHtml(p.tipo_pago || '') + ' · Crédito #' + p.idp
              + '      </div>'
              + '    </div>'
              + '    <div class="text-right ml-2" style="flex-shrink:0">'
              + '      <div class="prst-val">$' + pendiente + '</div>'
              + '      <div class="prst-meta">pendiente</div>'
              + '    </div>'
              + '  </div>'
              + '  <div class="d-flex align-items-center justify-content-between mt-1">'
              + '    <div>';

        if (tieneAtraso) {
            html += '<small class="text-danger font-weight-bold" style="font-size:11px">'
                  + '<i class="fas fa-exclamation-triangle mr-1"></i>'
                  + p.cuotas_atrasadas + ' atraso(s) · $' + atrasado + '</small>';
        }
        if (tieneHoy && !tieneAtraso) {
            html += '<small class="text-primary" style="font-size:11px">'
                  + '<i class="fas fa-calendar-day mr-1"></i>Cuota de hoy</small>';
        }

        html += '    </div>'
              + '    <small class="text-muted" style="font-size:11px">'
              + 'Ver cuotas <i class="fas fa-chevron-right"></i></small>'
              + '  </div>'
              + '</div>';
    });

    $('#prst-list').html(html);
}
