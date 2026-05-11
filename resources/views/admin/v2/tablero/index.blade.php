{{-- resources/views/admin/v2/tablero/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Dashboard V2
@endsection

@section('styles')
@include('admin.v2._partials.mobile-styles')
<style>
.stat-card {
    border-radius: 10px;
    border: none;
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.15) !important;
}
.stat-icon {
    font-size: 2.4rem;
    opacity: .25;
    position: absolute;
    right: 15px;
    bottom: 10px;
}
.stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; }
.stat-label { font-size: .78rem; opacity: .85; margin-top: 4px; }

.module-card {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: transform .15s, box-shadow .15s;
    text-decoration: none !important;
    color: inherit !important;
    display: block;
}
.module-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
}
.hoy-badge {
    font-size: .7rem;
    vertical-align: middle;
}
</style>
@endsection

@section('contenido')

{{-- ── Encabezado ────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center mb-3">
  <div>
    <h4 class="mb-0 font-weight-bold">
      <i class="fas fa-tachometer-alt mr-2 text-primary" aria-hidden="true"></i>
      Dashboard
    </h4>
    <small class="text-muted text-capitalize">{{ $fechaHoy }}</small>
  </div>
  <span class="badge badge-primary ml-2 px-2" style="font-size:.7rem">V2</span>
</div>

{{-- ── Fila 1: Tarjetas de resumen ───────────────────────────────────── --}}
<div class="row mb-3">

  {{-- Clientes --}}
  <div class="col-6 col-md-3 mb-3">
    <div class="card stat-card bg-gradient-info text-white shadow-sm position-relative p-3">
      <div class="stat-value">{{ number_format($totalClientes) }}</div>
      <div class="stat-label">Clientes registrados</div>
      <i class="fas fa-users stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  {{-- Préstamos activos --}}
  <div class="col-6 col-md-3 mb-3">
    <div class="card stat-card bg-gradient-success text-white shadow-sm position-relative p-3">
      <div class="stat-value">{{ number_format($totalPrestamos) }}</div>
      <div class="stat-label">Préstamos activos</div>
      <i class="fas fa-file-invoice-dollar stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  {{-- Por cobrar hoy --}}
  <div class="col-6 col-md-3 mb-3">
    <div class="card stat-card bg-gradient-warning text-white shadow-sm position-relative p-3">
      <div class="stat-value">${{ number_format($totalCobrar) }}</div>
      <div class="stat-label">
        Por cobrar hoy
        <br>
        <span class="font-weight-bold">{{ $cuotasPagadas }} pagadas</span>
        &middot;
        <span>{{ $cuotasPendientes }} pendientes</span>
      </div>
      <i class="fas fa-hand-holding-usd stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  {{-- Monto atrasado --}}
  <div class="col-6 col-md-3 mb-3">
    <div class="card stat-card bg-gradient-danger text-white shadow-sm position-relative p-3">
      <div class="stat-value">${{ number_format($montoAtrasado) }}</div>
      <div class="stat-label">Monto atrasado</div>
      <i class="fas fa-exclamation-triangle stat-icon" aria-hidden="true"></i>
    </div>
  </div>

</div>

{{-- ── Fila 2: Gastos del mes + accesos rápidos ──────────────────────── --}}
<div class="row mb-3">

  {{-- Gastos del mes --}}
  <div class="col-12 col-md-4 mb-3">
    <div class="card shadow-sm h-100">
      <div class="card-header py-2">
        <h6 class="mb-0 font-weight-bold">
          <i class="fas fa-receipt mr-1 text-muted" aria-hidden="true"></i>
          Gastos del mes
        </h6>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <div class="text-center">
          <div style="font-size:2rem; font-weight:700; color:#e74c3c">
            ${{ number_format($gastosMes) }}
          </div>
          <div class="text-muted" style="font-size:.8rem">Total en gastos este mes</div>
          <a href="{{ route('admin.v2.gasto.index') }}"
             class="btn btn-sm btn-outline-danger mt-2">
            <i class="fas fa-list mr-1" aria-hidden="true"></i>Ver gastos
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Accesos rápidos --}}
  <div class="col-12 col-md-8 mb-3">
    <div class="card shadow-sm h-100">
      <div class="card-header py-2">
        <h6 class="mb-0 font-weight-bold">
          <i class="fas fa-th-large mr-1 text-muted" aria-hidden="true"></i>
          Accesos rápidos
        </h6>
      </div>
      <div class="card-body">
        <div class="row">

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.cliente.index') }}" class="module-card p-3 text-center">
              <i class="fas fa-users fa-2x text-info mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Clientes</div>
            </a>
          </div>

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.prestamo.index') }}" class="module-card p-3 text-center">
              <i class="fas fa-file-invoice-dollar fa-2x text-success mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Préstamos</div>
            </a>
          </div>

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.pago_card.index') }}" class="module-card p-3 text-center">
              <i class="fas fa-calendar-check fa-2x text-warning mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Ruta / Cobros</div>
            </a>
          </div>

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.empleado.index') }}" class="module-card p-3 text-center">
              <i class="fas fa-id-badge fa-2x text-primary mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Empleados</div>
            </a>
          </div>

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.gasto.index') }}" class="module-card p-3 text-center">
              <i class="fas fa-receipt fa-2x text-danger mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Gastos</div>
            </a>
          </div>

          <div class="col-6 col-md-4 mb-2">
            <a href="{{ route('admin.v2.tablero.index') }}" class="module-card p-3 text-center bg-light">
              <i class="fas fa-tachometer-alt fa-2x text-secondary mb-1" aria-hidden="true"></i>
              <div style="font-size:.8rem; font-weight:600">Dashboard</div>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>

@endsection
