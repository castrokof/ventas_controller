{{-- resources/views/admin/v2/pago_card/tab-anulados.blade.php --}}
<div class="card-header bg-gradient-danger border-0">
  <div class="row align-items-center">
    <div class="col">
      <h6 class="mb-0 text-white font-weight-bold">
        <i class="fas fa-ban mr-1" aria-hidden="true"></i>
        Préstamos anulados
      </h6>
    </div>
  </div>
</div>

<div class="card-body table-responsive p-2">

  {{-- Skeleton --}}
  <div id="skeleton-anulados" aria-hidden="true">
    <table class="table table-sm" aria-hidden="true">
      <tbody>
        @for ($i = 0; $i < 3; $i++)
        <tr class="skeleton-row">
          <td><div class="skeleton-cell w-80">&nbsp;</div></td>
          <td><div class="skeleton-cell w-60">&nbsp;</div></td>
          <td><div class="skeleton-cell w-40">&nbsp;</div></td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>

  {{-- Tabla real --}}
  <div id="wrapper-anulados" style="display:none">
    <table id="anulados"
           class="table table-hover table-sm table-bordered"
           cellspacing="0" width="100%"
           aria-label="Listado de préstamos anulados"
           role="grid">
      <thead class="thead-light">
        <tr>
          <th scope="col">Acciones</th>
          <th scope="col">Datos del cliente</th>
          <th scope="col">Monto</th>
          <th scope="col">Fecha anulación</th>
        </tr>
      </thead>
      <tbody>
        {{-- Se carga vía DataTables / AJAX desde el JS --}}
      </tbody>
    </table>
  </div>

  {{-- Estado vacío si no hay registros --}}
  <div id="empty-anulados" class="text-center py-5" style="display:none" role="status" aria-live="polite">
    <i class="fas fa-folder-open fa-3x text-muted mb-3" aria-hidden="true"></i>
    <p class="text-muted mb-0">No hay préstamos anulados registrados.</p>
  </div>

</div>
