@extends("theme.$theme.layout")

@section('titulo')
    Festivos Extra — Argentina
@endsection

@section('styles')
<style>
.festivos-card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
}
#tabla-festivos td, #tabla-festivos th {
    vertical-align: middle;
}
</style>
@endsection

@section('contenido')
<div class="container-fluid">

  {{-- Cabecera --}}
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:8px">
    <div>
      <h4 class="mb-0">
        <i class="fas fa-calendar-times text-danger mr-2"></i>Festivos Extra
      </h4>
      <small class="text-muted">
        Puentes turísticos y otros días no laborables por decreto.
        Los feriados nacionales fijos y trasladables ya están calculados automáticamente.
      </small>
    </div>
  </div>

  <div class="row">

    {{-- ── Panel agregar ──────────────────────────────────────────── --}}
    <div class="col-12 col-lg-4 mb-3">
      <div class="card festivos-card h-100">
        <div class="card-header bg-danger text-white">
          <h6 class="mb-0"><i class="fas fa-plus-circle mr-1"></i>Agregar festivo</h6>
        </div>
        <div class="card-body">
          <div id="form-result" class="mb-2"></div>
          <div class="form-group">
            <label class="font-weight-bold">Fecha <span class="text-danger">*</span></label>
            <input type="date" id="inp-fecha" class="form-control form-control-sm"
                   min="{{ now()->format('Y-m-d') }}">
          </div>
          <div class="form-group">
            <label class="font-weight-bold">Descripción <small class="text-muted">(opcional)</small></label>
            <input type="text" id="inp-desc" class="form-control form-control-sm"
                   maxlength="120" placeholder="Ej. Puente turístico — Decreto 52/2026">
          </div>
          <button id="btn-agregar" class="btn btn-danger btn-block btn-sm">
            <i class="fas fa-save mr-1"></i>Guardar festivo
          </button>
        </div>
        <div class="card-footer text-muted" style="font-size:.78rem">
          <i class="fas fa-info-circle mr-1"></i>
          Festivos nacionales ya incluidos automáticamente:<br>
          <strong>Fijos:</strong> 1/1, 24/3, 2/4, 1/5, 25/5, 9/7, 8/12, 25/12<br>
          <strong>Trasladables:</strong> 17/6 (Güemes), 20/6 (Belgrano), 17/8 (San Martín),
          12/10 (Diversidad), 20/11 (Soberanía)<br>
          <strong>Pascua:</strong> Carnaval (Lun+Mar), Jueves Santo, Viernes Santo
        </div>
      </div>
    </div>

    {{-- ── Tabla festivos registrados ─────────────────────────────── --}}
    <div class="col-12 col-lg-8 mb-3">
      <div class="card festivos-card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-list mr-1 text-danger"></i>Festivos registrados</h6>
        </div>
        <div class="card-body p-0">
          <table id="tabla-festivos" class="table table-sm table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:130px">Fecha</th>
                <th>Día</th>
                <th>Descripción</th>
                <th style="width:70px" class="text-center">Eliminar</th>
              </tr>
            </thead>
            <tbody>
              @forelse($festivos as $f)
              <tr id="row-{{ $f->id }}">
                <td class="font-weight-bold">{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($f->fecha)->locale('es')->isoFormat('dddd') }}</td>
                <td>{{ $f->descripcion ?? '—' }}</td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-danger btn-eliminar"
                          data-id="{{ $f->id }}"
                          data-fecha="{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}"
                          title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr id="row-empty">
                <td colspan="4" class="text-center text-muted py-4">
                  <i class="fas fa-calendar-check fa-2x mb-2 d-block"></i>
                  No hay festivos extra registrados.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($festivos->count() > 0)
        <div class="card-footer text-muted" style="font-size:.78rem">
          {{ $festivos->count() }} festivo{{ $festivos->count() != 1 ? 's' : '' }} registrado{{ $festivos->count() != 1 ? 's' : '' }}
        </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
const FESTIVOS_URL  = '{{ url("admin/v2/festivos") }}';
const FESTIVOS_TOKEN = '{{ csrf_token() }}';

const diasSemana = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];

function showError(msg) {
    $('#form-result').html('<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle mr-1"></i>' + msg + '</div>');
}
function showOk(msg) {
    $('#form-result').html('<div class="alert alert-success py-2 mb-0"><i class="fas fa-check-circle mr-1"></i>' + msg + '</div>');
    setTimeout(function(){ $('#form-result').html(''); }, 3000);
}

$('#btn-agregar').on('click', function () {
    var fecha = $('#inp-fecha').val();
    var desc  = $('#inp-desc').val().trim();

    if (!fecha) { showError('Selecciona una fecha.'); return; }

    var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...');

    $.ajax({
        url:      FESTIVOS_URL,
        method:   'POST',
        dataType: 'json',
        data:     { _token: FESTIVOS_TOKEN, fecha: fecha, descripcion: desc },
        success: function (r) {
            if (!r.ok) { showError('Error al guardar.'); return; }

            showOk('Festivo guardado.');
            $('#inp-fecha').val('');
            $('#inp-desc').val('');

            // Insertar fila en tabla sin recargar
            var d = new Date(fecha + 'T12:00:00');
            var nombreDia = diasSemana[d.getDay()];
            var fechaFmt  = d.toLocaleDateString('es-AR', { day:'2-digit', month:'2-digit', year:'numeric' });

            $('#row-empty').remove();
            $('<tr id="row-' + r.id + '">'
                + '<td class="font-weight-bold">' + fechaFmt + '</td>'
                + '<td>' + nombreDia + '</td>'
                + '<td>' + (desc || '—') + '</td>'
                + '<td class="text-center">'
                +   '<button class="btn btn-sm btn-outline-danger btn-eliminar"'
                +     ' data-id="' + r.id + '" data-fecha="' + fechaFmt + '" title="Eliminar">'
                +     '<i class="fas fa-trash-alt"></i>'
                +   '</button>'
                + '</td>'
            + '</tr>').appendTo('#tabla-festivos tbody');
        },
        error: function (xhr) {
            var err = xhr.responseJSON;
            if (err && err.errors && err.errors.fecha) {
                showError(err.errors.fecha[0]);
            } else {
                showError('Error al guardar. Intenta de nuevo.');
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Guardar festivo');
        }
    });
});

$(document).on('click', '.btn-eliminar', function () {
    var id    = $(this).data('id');
    var fecha = $(this).data('fecha');
    var $row  = $('#row-' + id);

    Swal.fire({
        title: '¿Eliminar festivo?',
        text:  fecha,
        icon:  'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
    }).then(function (result) {
        if (!result.value) return;
        $.ajax({
            url:    FESTIVOS_URL + '/' + id,
            method: 'DELETE',
            data:   { _token: FESTIVOS_TOKEN },
            success: function (r) {
                if (r.ok) {
                    $row.fadeOut(300, function () {
                        $(this).remove();
                        if ($('#tabla-festivos tbody tr').length === 0) {
                            $('#tabla-festivos tbody').html(
                                '<tr id="row-empty"><td colspan="4" class="text-center text-muted py-4">'
                                + '<i class="fas fa-calendar-check fa-2x mb-2 d-block"></i>'
                                + 'No hay festivos extra registrados.</td></tr>'
                            );
                        }
                    });
                }
            },
        });
    });
});
</script>
@endsection
