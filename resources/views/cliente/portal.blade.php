<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis Préstamos</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
  body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
  .portal-header { background: linear-gradient(135deg, #1a237e 0%, #283593 100%); color: #fff; padding: 2rem 1rem 3rem; text-align: center; }
  .portal-header h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
  .portal-header p  { margin: .4rem 0 0; opacity: .85; font-size: .9rem; }
  .search-card { max-width: 480px; margin: -1.8rem auto 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
  .search-card .card-body { padding: 1.4rem; }
  .loan-card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); margin-bottom: 1rem; overflow: hidden; }
  .loan-card .loan-header { padding: .7rem 1rem; display: flex; align-items: center; justify-content: space-between; }
  .loan-card .loan-body { padding: 1rem; }
  .lbl { font-size: .72rem; color: #888; text-transform: uppercase; letter-spacing: .04em; }
  .val { font-weight: 600; color: #333; font-size: .95rem; }
  .stat-row { display: flex; gap: .5rem; margin-bottom: .8rem; flex-wrap: wrap; }
  .stat-box { flex: 1; min-width: 70px; background: #f8f9fa; border-radius: 8px; padding: .5rem; text-align: center; }
  .stat-box .n { font-size: 1.3rem; font-weight: 700; }
  .stat-box .t { font-size: .65rem; color: #888; }
  .progress { border-radius: 6px; }
  .cliente-info { max-width: 600px; margin: 0 auto 1rem; background: #fff; border-radius: 10px; padding: 1rem 1.2rem; box-shadow: 0 2px 8px rgba(0,0,0,.07); display: flex; align-items: center; gap: 1rem; }
  .cliente-avatar { width: 48px; height: 48px; border-radius: 50%; background: #1a237e; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
  .loans-container { max-width: 600px; margin: 0 auto; }
  .estado-badge { font-size: .75rem; padding: .3em .7em; border-radius: 20px; font-weight: 600; }
  .badge-activo    { background: #e3f2fd; color: #1565c0; }
  .badge-saldado   { background: #e8f5e9; color: #2e7d32; }
  .badge-anulado   { background: #fce4ec; color: #c62828; }
  @media(max-width:480px) {
    .portal-header { padding: 1.5rem 1rem 2.5rem; }
    .portal-header h1 { font-size: 1.3rem; }
  }
</style>
</head>
<body>

<div class="portal-header">
  <h1><i class="fas fa-hand-holding-usd mr-2"></i>Mis Préstamos</h1>
  <p>Consulta el estado de tus créditos</p>
</div>

<div class="container-fluid px-3" style="padding-top:.5rem">

  {{-- Formulario de búsqueda --}}
  <div class="search-card card border-0 mx-auto">
    <div class="card-body">
      <form method="GET" action="{{ url('cliente-portal') }}">
        <label class="font-weight-bold text-dark mb-1" style="font-size:.9rem">
          <i class="fas fa-id-card mr-1 text-primary"></i>Número de documento
        </label>
        <div class="input-group">
          <input type="number" name="documento" class="form-control form-control-lg"
                 placeholder="Ej: 10234567890"
                 value="{{ $documento ?? '' }}"
                 min="1" required autofocus>
          <div class="input-group-append">
            <button class="btn btn-primary px-3" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  @if($error)
  <div class="alert alert-warning text-center mx-auto" style="max-width:480px;border-radius:10px">
    <i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}
  </div>
  @endif

  @if($cliente)
  {{-- Info del cliente --}}
  <div class="cliente-info">
    <div class="cliente-avatar"><i class="fas fa-user"></i></div>
    <div>
      <div class="font-weight-bold" style="font-size:1.05rem">{{ $cliente->nombres }} {{ $cliente->apellidos }}</div>
      <small class="text-muted">{{ $cliente->tipo_documento }} {{ $cliente->documento }}</small><br>
      <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $cliente->ciudad }}{{ $cliente->ciudad && $cliente->pais ? ', ' : '' }}{{ $cliente->pais }}</small>
    </div>
  </div>

  <div class="loans-container">
    @if($prestamos->isEmpty())
    <div class="text-center text-muted py-4">
      <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
      No tienes préstamos registrados.
    </div>
    @else
    <h6 class="font-weight-bold mb-2 text-dark px-1">
      <i class="fas fa-list mr-1 text-primary"></i>
      {{ $prestamos->count() }} préstamo{{ $prestamos->count() != 1 ? 's' : '' }}
    </h6>

    @foreach($prestamos as $p)
    @php
      $estLabel = match($p->estado ?? '') {
          'P'     => ['txt' => 'Saldado',  'cls' => 'badge-saldado'],
          'A'     => ['txt' => 'Anulado',  'cls' => 'badge-anulado'],
          default => ['txt' => 'Activo',   'cls' => 'badge-activo'],
      };
      $pct = $p->cuotas > 0 ? round($p->cuotas_pagadas / $p->cuotas * 100) : 0;
      $barClass = $p->cuotas_atrasadas > 0 ? 'bg-danger' : ($pct >= 100 ? 'bg-success' : 'bg-primary');
    @endphp
    <div class="loan-card card border-0">
      <div class="loan-header @if($p->estado === 'P') bg-success @elseif($p->estado === 'A') bg-secondary @else bg-primary @endif text-white">
        <div>
          <span style="font-size:.75rem;opacity:.85">Préstamo #{{ $p->idp }}</span><br>
          <strong style="font-size:1.1rem">$ {{ number_format($p->monto, 0, ',', '.') }}</strong>
        </div>
        <span class="estado-badge {{ $estLabel['cls'] }}">{{ $estLabel['txt'] }}</span>
      </div>
      <div class="loan-body">
        <div class="stat-row">
          <div class="stat-box">
            <div class="n text-success">{{ $p->cuotas_pagadas }}</div>
            <div class="t">Pagadas</div>
          </div>
          <div class="stat-box">
            <div class="n text-danger">{{ $p->cuotas_atrasadas }}</div>
            <div class="t">Atrasadas</div>
          </div>
          <div class="stat-box">
            <div class="n text-primary">{{ $p->cuotas_pendientes_cnt }}</div>
            <div class="t">Pendientes</div>
          </div>
          <div class="stat-box">
            <div class="n">{{ $p->cuotas }}</div>
            <div class="t">Total cuotas</div>
          </div>
        </div>

        <div class="mb-2">
          <div class="d-flex justify-content-between mb-1">
            <small class="text-muted">Progreso de pago</small>
            <small class="font-weight-bold">{{ $pct }}%</small>
          </div>
          <div class="progress" style="height:8px">
            <div class="progress-bar {{ $barClass }}" style="width:{{ $pct }}%"></div>
          </div>
        </div>

        <div class="row small mt-2">
          <div class="col-6">
            <span class="lbl">Valor cuota</span><br>
            <span class="val">$ {{ number_format($p->valor_cuota ?? 0, 0, ',', '.') }}</span>
          </div>
          <div class="col-6">
            <span class="lbl">Saldo pendiente</span><br>
            <span class="val @if($p->monto_pendiente > 0) text-danger @else text-success @endif">
              $ {{ number_format($p->monto_pendiente ?? 0, 0, ',', '.') }}
            </span>
          </div>
          <div class="col-6 mt-2">
            <span class="lbl">Tipo de pago</span><br>
            <span class="val">{{ $p->tipo_pago ?? '—' }}</span>
          </div>
          <div class="col-6 mt-2">
            <span class="lbl">Fecha inicio</span><br>
            <span class="val">{{ $p->fecha_inicial ?? '—' }}</span>
          </div>
          @if($p->fecha_final)
          <div class="col-6 mt-2">
            <span class="lbl">Fecha fin</span><br>
            <span class="val">{{ $p->fecha_final }}</span>
          </div>
          @endif
        </div>

        @if($p->cuotas_atrasadas > 0)
        <div class="alert alert-danger py-2 mt-2 mb-0 small">
          <i class="fas fa-exclamation-triangle mr-1"></i>
          Tienes <strong>{{ $p->cuotas_atrasadas }}</strong> cuota{{ $p->cuotas_atrasadas != 1 ? 's' : '' }} atrasada{{ $p->cuotas_atrasadas != 1 ? 's' : '' }}.
          Por favor comunícate con tu asesor.
        </div>
        @endif
      </div>
    </div>
    @endforeach
    @endif
  </div>
  @endif

  <div class="text-center text-muted small mt-4 pb-4">
    <i class="fas fa-lock mr-1"></i>Información confidencial. Solo para uso del titular.
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
