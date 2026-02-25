{{-- resources/views/admin/v2/pago_card/tab-clientes.blade.php --}}
<div class="card-header bg-gradient-info border-0">
  <div class="row align-items-center">
    <div class="col">
      <h6 class="mb-0 text-white font-weight-bold">
        <i class="fas fa-users mr-1" aria-hidden="true"></i>
        Clientes
      </h6>
    </div>
    <div class="col-auto">
      <button type="button" id="create_cliente"
              class="btn btn-sm btn-light"
              data-toggle="modal" data-target="#modal-u-cli"
              aria-label="Crear nuevo cliente">
        <i class="fas fa-user-plus mr-1" aria-hidden="true"></i>
        Crear cliente
      </button>
    </div>
  </div>
</div>

<div class="card-body table-responsive p-2">

  {{-- Skeleton --}}
  <div id="skeleton-clientes" aria-hidden="true">
    <table class="table table-sm" aria-hidden="true">
      <tbody>
        @for ($i = 0; $i < 4; $i++)
        <tr class="skeleton-row">
          <td><div class="skeleton-cell w-80">&nbsp;</div></td>
          <td><div class="skeleton-cell w-60">&nbsp;</div></td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>

  {{-- Tabla real --}}
  <div id="wrapper-clientes" style="display:none">
    <table id="clientecard"
           class="table table-hover table-sm"
           cellspacing="0" width="100%"
           aria-label="Listado de clientes"
           role="grid">
      <thead class="thead-light">
        <tr>
          <th scope="col">Datos</th>
          <th scope="col">Orden</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

</div>
