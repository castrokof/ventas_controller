{{-- resources/views/admin/v2/pago_card/tab-prestamos.blade.php --}}
<div class="card-header bg-gradient-olive border-0">
  <div class="row align-items-center">
    <div class="col">
      <h6 class="mb-0 text-white font-weight-bold">
        <i class="fas fa-file-invoice-dollar mr-1" aria-hidden="true"></i>
        Préstamos activos
      </h6>
    </div>
    <div class="col-auto">
      <button type="button" id="create_prestamo"
              class="btn btn-sm btn-light"
              data-toggle="modal" data-target="#modal-pc"
              aria-label="Crear nuevo préstamo">
        <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i>
        Crear préstamo
      </button>
    </div>
  </div>
</div>

<div class="card-body table-responsive p-2 position-relative">

  {{-- Skeleton --}}
  <div id="skeleton-prestamos" aria-hidden="true">
    <table class="table table-sm" aria-hidden="true">
      <tbody>
        @for ($i = 0; $i < 4; $i++)
        <tr class="skeleton-row">
          <td><div class="skeleton-cell w-40" style="height:36px;border-radius:8px;">&nbsp;</div></td>
          <td><div class="skeleton-cell w-80">&nbsp;</div></td>
          <td><div class="skeleton-cell w-60">&nbsp;</div></td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>

  {{-- Tabla real --}}
  <div id="wrapper-prestamos" style="display:none">
    <table id="prestamos"
           class="table table-hover table-sm"
           cellspacing="0" width="100%"
           aria-label="Listado de préstamos activos"
           role="grid">
      <thead class="thead-light">
        <tr>
          <th scope="col">Acciones</th>
          <th scope="col">Datos del cliente</th>
          <th scope="col">Orden</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

</div>
