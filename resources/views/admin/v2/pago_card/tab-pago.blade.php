{{-- resources/views/admin/v2/pago_card/tab-pago.blade.php --}}
<div class="card-header bg-gradient-success border-0">
  <div class="row align-items-center">

    {{-- Título --}}
    <div class="col-12 col-md-3 mb-2 mb-md-0">
      <h6 class="mb-0 text-white font-weight-bold">
        <i class="fas fa-money-bill-wave mr-1" aria-hidden="true"></i>
        Gestión de Pagos
      </h6>
    </div>

    {{-- Filtro de estado --}}
    <div class="col-12 col-md-6 mb-2 mb-md-0">
      <label for="estado_pago" class="sr-only">Seleccione tipo de pagos</label>
      <select name="estado_pago" id="estado_pago"
              class="form-control form-control-sm select2bs4"
              style="width:100%"
              aria-label="Filtrar pagos por estado">
        <option value="">— Seleccione tipo de pagos —</option>
        <option value="0">
          <i class="fas fa-calendar-day"></i> Pagos por cobrar del día
        </option>
        <option value="1">Pagos registrados del día</option>
        <option value="4">Pagos por cobrar del día (por préstamo)</option>
        <option value="5">Pagos registrados del día (por préstamo)</option>
      </select>
    </div>

    {{-- Leyenda de estados --}}
    <div class="col-12 col-md-3 text-md-right">
      <span class="badge badge-warning mr-1" title="Cuota pendiente">C</span>
      <span class="badge badge-success mr-1" title="Cuota pagada">P</span>
      <span class="badge badge-danger mr-1"  title="Cuota atrasada">A</span>
      <span class="badge badge-info"         title="Préstamo cancelado">T</span>
    </div>

  </div>
</div>

<div class="card-body p-2 position-relative">

  {{-- Skeleton mientras carga la tabla --}}
  <div id="skeleton-pago" aria-hidden="true">
    <table class="table table-sm" aria-hidden="true">
      <tbody>
        @for ($i = 0; $i < 5; $i++)
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
  <div id="wrapper-pago" style="display:none">
    <table id="pago"
           class="table table-hover table-sm"
           cellspacing="0" width="100%"
           aria-label="Listado de pagos"
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
