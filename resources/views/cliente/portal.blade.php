<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis Préstamos</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
  :root { --blue-dark:#1a237e; --blue:#283593; }
  * { box-sizing: border-box; }
  body { background:#f0f2f5; font-family:'Segoe UI',system-ui,sans-serif; margin:0; }

  /* Nav */
  .portal-nav {
    background:linear-gradient(135deg,var(--blue-dark) 0%,#3949ab 100%);
    color:#fff; padding:.75rem 1rem;
    display:flex; align-items:center; justify-content:space-between;
    position:sticky; top:0; z-index:100;
    box-shadow:0 2px 8px rgba(0,0,0,.2);
  }
  .portal-nav .brand { font-weight:700; font-size:.95rem; }
  .portal-nav .nav-links { display:flex; gap:.9rem; align-items:center; }
  .portal-nav a {
    color:rgba(255,255,255,.85); font-size:.8rem;
    text-decoration:none; display:flex; align-items:center; gap:.3rem;
  }
  .portal-nav a:hover { color:#fff; }

  /* Header del cliente */
  .cli-header {
    background:linear-gradient(135deg,var(--blue-dark) 0%,#3949ab 100%);
    color:#fff; padding:1.2rem 1rem 2.5rem;
  }
  .cli-header .cli-name { font-size:1.15rem; font-weight:700; }
  .cli-header .cli-doc  { font-size:.8rem; opacity:.8; margin-top:.1rem; }

  /* Contenido */
  .content-wrap { max-width:600px; margin:-1.5rem auto 0; padding:0 .75rem 4rem; }

  /* Loan cards */
  .loan-card  { background:#fff; border-radius:14px; margin-bottom:1rem; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
  .loan-head  { padding:.85rem 1rem; display:flex; justify-content:space-between; align-items:flex-start; }
  .loan-body  { padding:.9rem 1rem 0; }

  /* Stats */
  .stat-row { display:flex; gap:.5rem; margin-bottom:.85rem; }
  .stat-box  { flex:1; background:#f8f9fa; border-radius:10px; padding:.55rem .3rem; text-align:center; }
  .stat-box .n { font-size:1.35rem; font-weight:700; line-height:1; }
  .stat-box .t { font-size:.62rem; color:#888; margin-top:.15rem; }

  /* Progress */
  .prog-wrap { margin-bottom:.9rem; }
  .prog-lbl  { display:flex; justify-content:space-between; font-size:.75rem; color:#666; margin-bottom:.3rem; }
  .progress  { height:9px; border-radius:5px; background:#e9ecef; }

  /* Cuotas list */
  .cuotas-toggle {
    width:100%; border:none; background:none; padding:.7rem 1rem;
    text-align:left; font-size:.85rem; font-weight:600;
    color:var(--blue-dark); display:flex; align-items:center;
    justify-content:space-between; cursor:pointer;
    border-top:1px solid #f0f0f0;
  }
  .cuotas-toggle:focus { outline:none; }
  .cuotas-body { display:none; padding:0 1rem .9rem; }
  .cuotas-body.open { display:block; }
  .cuota-row {
    display:flex; align-items:center; gap:.5rem;
    padding:.45rem .5rem; border-radius:8px; margin-bottom:.3rem; font-size:.83rem;
  }
  .cuota-row.atrasada  { background:#fff3f3; }
  .cuota-row.pendiente { background:#f5f7ff; }
  .cuota-row .ci { width:22px; text-align:center; flex-shrink:0; }
  .cuota-row .cn { flex:1; }
  .cuota-row .cn .fecha { font-size:.72rem; color:#888; }
  .cuota-row .cv { font-weight:700; white-space:nowrap; }
  .cuota-row .cs { font-size:.72rem; font-weight:600; white-space:nowrap; }
  .cs.atrasada  { color:#c62828; }
  .cs.pendiente { color:#1565c0; }
  .cs.proxima   { color:#e65100; }

  /* Estado badges */
  .lbadge { font-size:.72rem; padding:.28em .65em; border-radius:20px; font-weight:600; white-space:nowrap; }
  .lb-act  { background:#e3f2fd; color:#1565c0; }
  .lb-paid { background:#e8f5e9; color:#2e7d32; }
  .lb-can  { background:#f3e5f5; color:#6a1b9a; }

  .footer-note { text-align:center; color:#aaa; font-size:.75rem; padding:1.5rem 0 3rem; }
</style>
</head>
<body>

{{-- Nav --}}
<nav class="portal-nav">
  <div class="brand"><i class="fas fa-hand-holding-usd mr-1"></i>Mis préstamos</div>
  <div class="nav-links">
    <a href="{{ route('cliente.portal.change_password_form') }}">
      <i class="fas fa-lock"></i><span class="d-none d-sm-inline">Cambiar contraseña</span>
    </a>
    <a href="{{ route('cliente.portal.logout') }}">
      <i class="fas fa-sign-out-alt"></i><span class="d-none d-sm-inline">Salir</span>
    </a>
  </div>
</nav>

{{-- Header cliente --}}
<div class="cli-header">
  <div class="d-flex align-items-center" style="gap:.75rem">
    <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);
                display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0">
      <i class="fas fa-user"></i>
    </div>
    <div>
      <div class="cli-name">{{ $cliente->nombres }} {{ $cliente->apellidos }}</div>
      <div class="cli-doc">{{ $cliente->tipo_documento }} {{ $cliente->documento }}</div>
    </div>
  </div>
</div>

{{-- Contenido --}}
<div class="content-wrap">

  @if($prestamos->isEmpty())
  <div class="text-center text-muted py-5">
    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#ccc"></i>
    No tienes préstamos registrados.
  </div>
  @else

  @php $today = now()->toDateString(); @endphp

  <h6 class="font-weight-bold mb-2 text-dark px-1 mt-3">
    <i class="fas fa-list mr-1 text-primary"></i>
    {{ $prestamos->count() }} préstamo{{ $prestamos->count() != 1 ? 's' : '' }}
  </h6>

  @foreach($prestamos as $p)
  @php
    $isActive = !in_array($p->estado ?? '', ['P','A']);
    $isPaid   = ($p->estado ?? '') === 'P';
    $isCan    = ($p->estado ?? '') === 'A';
    $headCls  = $isPaid ? 'bg-success' : ($isCan ? 'bg-secondary' : 'bg-primary');
    $lbCls    = $isPaid ? 'lb-paid'    : ($isCan ? 'lb-can'       : 'lb-act');
    $lbTxt    = $isPaid ? 'Saldado'    : ($isCan ? 'Anulado'      : 'Activo');
    $pct      = $p->cuotas > 0 ? min(100, round($p->cuotas_pagadas / $p->cuotas * 100)) : 0;
    $barCls   = $p->cuotas_atrasadas > 0 ? 'bg-danger' : ($pct >= 100 ? 'bg-success' : 'bg-primary');
    $lCuotas  = $cuotas->get($p->idp, collect());
  @endphp

  <div class="loan-card">
    <div class="loan-head text-white {{ $headCls }}">
      <div>
        <div style="font-size:.72rem;opacity:.8">Préstamo #{{ $p->idp }}</div>
        <div style="font-size:1.2rem;font-weight:700">$ {{ number_format($p->monto, 0, ',', '.') }}</div>
        @if($p->tipo_pago)
        <div style="font-size:.75rem;opacity:.8"><i class="fas fa-calendar-alt mr-1"></i>{{ $p->tipo_pago }}</div>
        @endif
      </div>
      <div class="text-right">
        <span class="lbadge {{ $lbCls }}">{{ $lbTxt }}</span>
        @if($isActive && $p->cuotas_atrasadas > 0)
        <br><span class="lbadge mt-1 d-inline-block" style="background:#c62828;color:#fff">
          <i class="fas fa-exclamation-triangle mr-1"></i>{{ $p->cuotas_atrasadas }} atr.
        </span>
        @endif
      </div>
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
          <div class="t">Total</div>
        </div>
      </div>

      <div class="prog-wrap">
        <div class="prog-lbl">
          <span>Avance de pago</span>
          <span class="font-weight-bold">{{ $pct }}%</span>
        </div>
        <div class="progress">
          <div class="progress-bar {{ $barCls }}" style="width:{{ $pct }}%"></div>
        </div>
      </div>

      <div class="row small mb-2">
        <div class="col-6">
          <span style="color:#888;font-size:.7rem">VALOR CUOTA</span><br>
          <span class="font-weight-bold">$ {{ number_format($p->valor_cuota ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="col-6 text-right">
          <span style="color:#888;font-size:.7rem">SALDO PENDIENTE</span><br>
          <span class="font-weight-bold {{ $p->monto_pendiente > 0 ? 'text-danger' : 'text-success' }}">
            $ {{ number_format($p->monto_pendiente ?? 0, 0, ',', '.') }}
          </span>
        </div>
      </div>

      @if($p->fecha_inicial || $p->fecha_final)
      <div class="row small mb-2" style="color:#888;font-size:.72rem">
        @if($p->fecha_inicial)
        <div class="col-6"><i class="fas fa-calendar-plus mr-1"></i>Inicio: {{ $p->fecha_inicial }}</div>
        @endif
        @if($p->fecha_final)
        <div class="col-6 text-right"><i class="fas fa-calendar-check mr-1"></i>Fin: {{ $p->fecha_final }}</div>
        @endif
      </div>
      @endif
    </div>

    {{-- Cuotas pendientes --}}
    @if($lCuotas->isNotEmpty())
    <button class="cuotas-toggle" onclick="toggleCuotas(this)">
      <span>
        <i class="fas fa-list-ul mr-1"></i>Cuotas por pagar
        @php $nAtr = $lCuotas->where('estado','A')->count(); @endphp
        @if($nAtr > 0)
        <span class="badge badge-danger ml-1">{{ $nAtr }} atrasada{{ $nAtr != 1 ? 's' : '' }}</span>
        @endif
        <span class="badge badge-light ml-1">{{ $lCuotas->count() }}</span>
      </span>
      <i class="fas fa-chevron-down toggle-icon" style="font-size:.8rem;transition:transform .25s"></i>
    </button>
    <div class="cuotas-body {{ $isActive && $p->cuotas_atrasadas > 0 ? 'open' : '' }}">
      @foreach($lCuotas as $c)
      @php
        $isAtr  = $c->estado === 'A';
        $isProx = !$isAtr && $c->fecha_cuota && $c->fecha_cuota <= now()->addDays(7)->toDateString();
        $rowCls = $isAtr ? 'atrasada' : 'pendiente';
        $icon   = $isAtr ? 'fas fa-exclamation-circle text-danger' : 'far fa-clock text-primary';
        $statLbl = $isAtr ? 'Atrasada' : ($isProx ? 'Próxima' : 'Pendiente');
        $statCls = $isAtr ? 'atrasada' : ($isProx ? 'proxima' : 'pendiente');
      @endphp
      <div class="cuota-row {{ $rowCls }}">
        <div class="ci"><i class="{{ $icon }}"></i></div>
        <div class="cn">
          <div>Cuota #{{ $c->d_numero_cuota }}</div>
          <div class="fecha"><i class="fas fa-calendar-day mr-1"></i>{{ $c->fecha_cuota ?? '—' }}</div>
        </div>
        <div class="cv">$ {{ number_format($c->valor_cuota ?? 0, 0, ',', '.') }}</div>
        <div class="cs {{ $statCls }}">{{ $statLbl }}</div>
      </div>
      @endforeach
    </div>
    @elseif($isActive)
    <div class="px-3 pb-3 pt-1 small text-success">
      <i class="fas fa-check-circle mr-1"></i>¡Al día! No tienes cuotas pendientes.
    </div>
    @endif

  </div>
  @endforeach
  @endif

  <div class="footer-note mt-3">
    <i class="fas fa-lock mr-1"></i>Información confidencial · Solo para uso del titular
  </div>
</div>

<script>
function toggleCuotas(btn) {
    var body = btn.nextElementSibling;
    var icon = btn.querySelector('.toggle-icon');
    var open = body.classList.toggle('open');
    icon.style.transform = open ? 'rotate(180deg)' : '';
}
</script>
</body>
</html>
