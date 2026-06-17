{{-- resources/views/admin/v2/gps/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    GPS — Monitoreo de Cobradores
@endsection

@section('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#mapa-gps {
    height: 420px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
}
.gps-card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px 16px;
    text-align: center;
}
.stat-box .stat-val { font-size: 1.5rem; font-weight: 700; color: #6366f1; }
.stat-box .stat-lbl { font-size: .75rem; color: #6c757d; }
#gps-status {
    font-size: .78rem;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.dot-activo  { width:8px;height:8px;border-radius:50%;background:#22c55e;animation:blink 1s infinite; }
.dot-inactivo{ width:8px;height:8px;border-radius:50%;background:#94a3b8; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.leaflet-popup-content { font-size: .8rem; }
</style>
@endsection

@section('contenido')
<div class="container-fluid">

{{-- ── Cabecera ──────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:8px">
    <div>
        <h4 class="mb-0"><i class="fas fa-map-marked-alt text-primary mr-2"></i>GPS — Monitoreo de Cobradores</h4>
        <small class="text-muted" id="txt-subtitulo">Ruta del día · punto GPS cada 3 minutos · estimación de consumo de gasolina</small>
    </div>
    <div id="gps-status" class="bg-light text-muted border">
        <span class="dot-inactivo" id="dot-status"></span>
        <span id="txt-status">Rastreo inactivo</span>
    </div>
</div>

{{-- ── Selector de vista ─────────────────────────────────────── --}}
<div class="btn-group btn-group-sm mb-3" role="group" id="grp-modo-gps">
    <button type="button" class="btn btn-primary active" data-modo="ruta">
        <i class="fas fa-route mr-1"></i>Ruta de seguimiento
    </button>
    <button type="button" class="btn btn-outline-primary" data-modo="pagos">
        <i class="fas fa-map-pin mr-1"></i>Puntos de cobro
    </button>
</div>

{{-- ── Filtros ───────────────────────────────────────────────── --}}
<div class="card gps-card mb-3">
    <div class="card-body py-2">
        <div class="row align-items-end" style="gap:8px 0">
            <div class="col-md-3 col-6">
                <label class="small mb-1">Cobrador</label>
                <select id="sel-usuario" class="form-control form-control-sm">
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ session('usuario_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->usuario }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label class="small mb-1">Fecha</label>
                <input type="date" id="sel-fecha" class="form-control form-control-sm"
                       value="{{ now('America/Argentina/Buenos_Aires')->toDateString() }}">
            </div>
            <div class="col-md-2 col-6 campo-ruta">
                <label class="small mb-1">Rendimiento (km/L)</label>
                <input type="number" id="inp-rendimiento" class="form-control form-control-sm"
                       value="40" min="1" step="0.5">
            </div>
            <div class="col-md-2 col-6 campo-ruta">
                <label class="small mb-1">Precio combustible ($/L)</label>
                <input type="number" id="inp-precio" class="form-control form-control-sm"
                       value="1500" min="1" step="1">
            </div>
            <div class="col-md-2 col-12">
                <button id="btn-cargar" class="btn btn-primary btn-sm btn-block mt-1">
                    <i class="fas fa-search mr-1"></i>Ver ruta
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Resumen ───────────────────────────────────────────────── --}}
<div class="row mb-3" id="resumen-gps" style="display:none!important">
    <div class="col-6 col-md-3 mb-2">
        <div class="stat-box">
            <div class="stat-val" id="st-puntos">0</div>
            <div class="stat-lbl" id="st-puntos-lbl">Puntos registrados</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2 campo-ruta">
        <div class="stat-box">
            <div class="stat-val" id="st-km">0</div>
            <div class="stat-lbl">Kilómetros recorridos</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2 campo-ruta">
        <div class="stat-box">
            <div class="stat-val" id="st-litros">0</div>
            <div class="stat-lbl">Litros consumidos</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2 campo-ruta">
        <div class="stat-box">
            <div class="stat-val" id="st-costo">$0</div>
            <div class="stat-lbl">Costo estimado gasolina</div>
        </div>
    </div>
</div>

{{-- ── Mapa ──────────────────────────────────────────────────── --}}
<div class="card gps-card">
    <div class="card-body p-2">
        <div id="mapa-gps"></div>
        <p id="mapa-msg" class="text-center text-muted py-4" style="font-size:.85rem">
            <i class="fas fa-map-pin mr-1"></i>Selecciona un cobrador y fecha y pulsa "Ver ruta".
        </p>
    </div>
</div>

</div>
@endsection

@section('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const BASE_GPS = '{{ url("admin/v2/gps") }}';
const GPS_TOKEN = '{{ csrf_token() }}';
const ROL_ID    = {{ (int) session('rol_id') }};
const MI_UID    = {{ (int) session('usuario_id') }};

/* ── Mapa Leaflet ──────────────────────────────────────────────── */
var map        = null;
var rutaLine   = null;
var markers    = [];
var modoVista  = 'ruta'; // 'ruta' = seguimiento cada 3 min · 'pagos' = puntos de cobro

function initMapa() {
    if (map) return;
    map = L.map('mapa-gps').setView([-34.6, -58.4], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);
}

function limpiarMapa() {
    if (rutaLine)  { map.removeLayer(rutaLine); rutaLine = null; }
    markers.forEach(function(m) { map.removeLayer(m); });
    markers = [];
}

/* ── Vista "Ruta de seguimiento": polilínea conectando los puntos ── */
function dibujarRuta(puntos) {
    limpiarMapa();
    if (!puntos || !puntos.length) {
        $('#mapa-msg').show().text('Sin puntos registrados para esta fecha / cobrador.');
        $('#resumen-gps').css('display', 'none!important');
        return;
    }
    $('#mapa-msg').hide();

    var latlngs = puntos.map(function(p) {
        return [parseFloat(p.latitud), parseFloat(p.longitud)];
    });

    rutaLine = L.polyline(latlngs, { color: '#6366f1', weight: 4, opacity: .85 }).addTo(map);

    /* Marcador inicio (verde) */
    var startIcon = L.divIcon({ className: '', html:
        '<div style="background:#22c55e;width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.4)"></div>',
        iconSize:[14,14], iconAnchor:[7,7]
    });
    /* Marcador fin (rojo) */
    var endIcon = L.divIcon({ className: '', html:
        '<div style="background:#ef4444;width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.4)"></div>',
        iconSize:[14,14], iconAnchor:[7,7]
    });

    var mStart = L.marker(latlngs[0], { icon: startIcon }).addTo(map);
    mStart.bindTooltip('Inicio: ' + (puntos[0].created_at || ''), { permanent: false });
    markers.push(mStart);

    if (latlngs.length > 1) {
        var mEnd = L.marker(latlngs[latlngs.length - 1], { icon: endIcon }).addTo(map);
        mEnd.bindTooltip('Último punto: ' + (puntos[puntos.length - 1].created_at || ''), { permanent: false });
        markers.push(mEnd);
    }

    map.fitBounds(rutaLine.getBounds(), { padding: [20, 20] });
}

/* ── Vista "Puntos de cobro": un marcador suelto por cada pago,      ──
   ── sin conectar (no es un trayecto, son cobros independientes)    ── */
var cobroIcon = L.divIcon({ className: '', html:
    '<div style="background:#f59e0b;width:18px;height:18px;border-radius:50%;border:2px solid #fff;' +
    'box-shadow:0 0 4px rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;' +
    'color:#fff;font-size:10px;font-weight:700">$</div>',
    iconSize:[18,18], iconAnchor:[9,9]
});

function dibujarPuntosPago(puntos) {
    limpiarMapa();
    if (!puntos || !puntos.length) {
        $('#mapa-msg').show().text('Sin cobros con ubicación registrada para esta fecha / cobrador.');
        $('#resumen-gps').css('display', 'none!important');
        return;
    }
    $('#mapa-msg').hide();

    puntos.forEach(function(p) {
        var lat = parseFloat(p.latitud), lng = parseFloat(p.longitud);
        var m = L.marker([lat, lng], { icon: cobroIcon }).addTo(map);
        var valor = Number(p.valor_abono || 0).toLocaleString('es-CO');
        var cliente = [p.nombres, p.apellidos].filter(Boolean).join(' ');
        var html =
            '<div>' +
            '<b>Fecha:</b> ' + (p.fecha_pago || '') + '<br>' +
            '<b>Crédito #:</b> ' + p.prestamo_id + ' &middot; cuota ' + p.numero_cuota + '<br>' +
            '<b>Cliente:</b> ' + (cliente || '—') + '<br>' +
            '<b>Documento:</b> ' + (p.documento || '—') + '<br>' +
            '<b>Valor cobrado:</b> $' + valor +
            '</div>';
        m.bindPopup(html);
        markers.push(m);
    });

    var grupo = L.featureGroup(markers);
    map.fitBounds(grupo.getBounds(), { padding: [30, 30], maxZoom: 17 });
}

/* ── Alternar entre las dos vistas ────────────────────────────── */
function aplicarModoUI() {
    $('#grp-modo-gps button').removeClass('active btn-primary').addClass('btn-outline-primary');
    $('#grp-modo-gps button[data-modo="' + modoVista + '"]').addClass('active btn-primary').removeClass('btn-outline-primary');

    if (modoVista === 'ruta') {
        $('.campo-ruta').show();
        $('#btn-cargar').html('<i class="fas fa-search mr-1"></i>Ver ruta');
        $('#txt-subtitulo').text('Ruta del día · punto GPS cada 3 minutos · estimación de consumo de gasolina');
        $('#st-puntos-lbl').text('Puntos registrados');
    } else {
        $('.campo-ruta').hide();
        $('#btn-cargar').html('<i class="fas fa-search mr-1"></i>Ver cobros');
        $('#txt-subtitulo').text('Puntos donde se registró el cobro de cada cuota (no es un trayecto continuo)');
        $('#st-puntos-lbl').text('Cobros registrados');
    }
}

$('#grp-modo-gps button').on('click', function () {
    modoVista = $(this).data('modo');
    aplicarModoUI();
    if ($('#sel-usuario').val() && $('#sel-fecha').val()) {
        cargarDatos();
    }
});

/* ── Cargar datos según la vista activa ──────────────────────────── */
function cargarDatos() {
    if (modoVista === 'ruta') {
        cargarRuta();
    } else {
        cargarPuntosPago();
    }
}

function cargarRuta() {
    var uid   = $('#sel-usuario').val();
    var fecha = $('#sel-fecha').val();
    if (!uid || !fecha) return;

    $('#btn-cargar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Cargando...');
    initMapa();

    $.ajax({
        url:      BASE_GPS + '/datos',
        method:   'GET',
        dataType: 'json',
        data:     { usuario_id: uid, fecha: fecha },
        success: function(resp) {
            if (!resp.ok) {
                $('#mapa-msg').show().text('Error al cargar datos.');
                return;
            }
            var puntos      = resp.puntos     || [];
            var distanciaKm = parseFloat(resp.distancia_km || 0);
            var rendimiento = parseFloat($('#inp-rendimiento').val()) || 40;
            var precio      = parseFloat($('#inp-precio').val())      || 1500;
            var litros      = rendimiento > 0 ? distanciaKm / rendimiento : 0;
            var costo       = litros * precio;

            $('#st-puntos').text(puntos.length);
            $('#st-km').text(distanciaKm.toFixed(2) + ' km');
            $('#st-litros').text(litros.toFixed(2) + ' L');
            $('#st-costo').text('$' + Math.round(costo).toLocaleString('es-CO'));

            if (puntos.length) {
                $('#resumen-gps').removeAttr('style').show();
                aplicarModoUI();
            } else {
                $('#resumen-gps').css('display', 'none!important');
            }

            dibujarRuta(puntos);
        },
        error: function() {
            $('#mapa-msg').show().text('Error de red al cargar la ruta.');
        },
        complete: function() {
            $('#btn-cargar').prop('disabled', false).html('<i class="fas fa-search mr-1"></i>Ver ruta');
        }
    });
}

function cargarPuntosPago() {
    var uid   = $('#sel-usuario').val();
    var fecha = $('#sel-fecha').val();
    if (!uid || !fecha) return;

    $('#btn-cargar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Cargando...');
    initMapa();

    $.ajax({
        url:      BASE_GPS + '/pagos',
        method:   'GET',
        dataType: 'json',
        data:     { usuario_id: uid, fecha: fecha },
        success: function(resp) {
            if (!resp.ok) {
                $('#mapa-msg').show().text('Error al cargar datos.');
                return;
            }
            var puntos = resp.puntos || [];

            $('#st-puntos').text(puntos.length);

            if (puntos.length) {
                $('#resumen-gps').removeAttr('style').show();
                aplicarModoUI();
            } else {
                $('#resumen-gps').css('display', 'none!important');
            }

            dibujarPuntosPago(puntos);
        },
        error: function() {
            $('#mapa-msg').show().text('Error de red al cargar los cobros.');
        },
        complete: function() {
            $('#btn-cargar').prop('disabled', false).html('<i class="fas fa-search mr-1"></i>Ver cobros');
        }
    });
}

/* ── GPS activo: enviar posición cada 3 minutos ─────────────────── */
var watchId    = null;
var gpsActivo  = false;
var INTERVALO_MS = 3 * 60 * 1000; // 3 minutos

function activarGps() {
    if (!navigator.geolocation) return;

    var lastSent = 0;
    watchId = navigator.geolocation.watchPosition(
        function(pos) {
            var ahora = Date.now();
            if (ahora - lastSent < INTERVALO_MS) return;
            lastSent = ahora;

            $('#dot-status').attr('class', 'dot-activo');
            $('#txt-status').text('Rastreo activo');

            $.ajax({
                url:    BASE_GPS + '/registrar',
                method: 'POST',
                data: {
                    _token:    GPS_TOKEN,
                    latitud:   pos.coords.latitude,
                    longitud:  pos.coords.longitude,
                    precision: pos.coords.accuracy,
                    velocidad: pos.coords.speed ? (pos.coords.speed * 3.6).toFixed(1) : null,
                },
                dataType: 'json',
            });
        },
        function(err) {
            $('#txt-status').text('GPS no disponible');
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 }
    );
}

$(function() {
    aplicarModoUI();

    /* Cargar hoy al abrir si hay un cobrador seleccionado */
    if ($('#sel-usuario').val()) {
        initMapa();
        cargarDatos();
    }

    $('#btn-cargar').on('click', cargarDatos);

    /* Recalcular estimación sin recargar mapa cuando cambia rendimiento/precio */
    $('#inp-rendimiento, #inp-precio').on('input', function() {
        if (modoVista !== 'ruta') return;
        var kmText = $('#st-km').text().replace(' km', '');
        var km     = parseFloat(kmText) || 0;
        if (!km) return;
        var rendimiento = parseFloat($('#inp-rendimiento').val()) || 40;
        var precio      = parseFloat($('#inp-precio').val())      || 1500;
        var litros      = rendimiento > 0 ? km / rendimiento : 0;
        var costo       = litros * precio;
        $('#st-litros').text(litros.toFixed(2) + ' L');
        $('#st-costo').text('$' + Math.round(costo).toLocaleString('es-CO'));
    });

    /* Activar GPS solo en dispositivos del cobrador (no en desktop admin) */
    activarGps();
});
</script>
@endsection
