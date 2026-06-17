{{-- resources/views/admin/v2/pago_card/form-prestamo.blade.php --}}
{{-- IMPORTANTE: los name= NO cambian para compatibilidad con backend --}}
{{-- $idSuffix permite incluir este formulario más de una vez en la misma página
     (p.ej. crear + refinanciar) sin que los id= se dupliquen en el DOM --}}
@php($idSuffix = $idSuffix ?? '')

{{-- ── Fila 1: cliente + monto + tipo + cuotas + interés ──── --}}
<div class="form-group row">

  <div class="col-12 col-md-4">
    <label for="cliente_id{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-user fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cliente <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="cliente_id" id="cliente_id{{ $idSuffix }}"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Seleccione el cliente">
      <option value="">— Seleccione documento / cliente —</option>
      @foreach ($clientes as $cliente)
        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
          {{ $cliente->documento }} → {{ $cliente->nombres }} {{ $cliente->apellidos }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-6 col-md-2">
    <label for="montop{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-dollar-sign fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" inputmode="numeric" name="monto" id="montop{{ $idSuffix }}"
             class="form-control font-weight-bold prestamo-montop"
             style="font-size:1.15rem"
             value="{{ old('monto', $data->monto ?? '') }}"
             required aria-required="true"
             aria-label="Monto del préstamo"
             placeholder="0"
             autocomplete="off">
    </div>
  </div>

  <div class="col-6 col-md-2">
    <label for="tipo_pagop{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-clock fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Tipo de pago <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_pago" id="tipo_pagop{{ $idSuffix }}"
            class="form-control form-control-sm select2bs4 prestamo-tipo-pagop"
            style="width:100%"
            required aria-required="true"
            aria-label="Frecuencia de pago">
      <option value="">— Seleccione —</option>
      <option value="Diario">Diario</option>
      <option value="Semanal">Semanal</option>
      <option value="Quincenal">Quincenal</option>
      <option value="Mensual">Mensual</option>
    </select>
  </div>

  <div class="col-6 col-md-2">
    <label for="cuotas{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-list-ol fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cuotas <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="number" name="cuotas" id="cuotas{{ $idSuffix }}"
           class="form-control form-control-sm prestamo-cuotas"
           value="{{ old('cuotas', $data->cuotas ?? '') }}"
           min="1" step="1" required aria-required="true"
           aria-label="Número de cuotas"
           placeholder="Ej. 30">
  </div>

  <div class="col-6 col-md-2">
    <label for="interes{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-percentage fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Interés <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <input type="number" name="interes" id="interes{{ $idSuffix }}"
             class="form-control prestamo-interes"
             value="{{ old('interes', $data->interes ?? '') }}"
             min="0" step="any" required aria-required="true"
             aria-label="Porcentaje de interés"
             placeholder="Ej. 20">
      <div class="input-group-append">
        <span class="input-group-text" aria-hidden="true">%</span>
      </div>
    </div>
  </div>

</div>

{{-- ── Fila 2: totales calculados + fecha + usuario ────────── --}}
<div class="form-group row">

  <div class="col-6 col-md-3">
    <label for="monto_totalp{{ $idSuffix }}" class="font-weight-bold">
      <i class="fas fa-calculator fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto total
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="monto_total" id="monto_totalp{{ $idSuffix }}"
             class="form-control bg-light font-weight-bold prestamo-monto-totalp"
             style="font-size:1.15rem"
             value="{{ old('monto_total', $data->monto_total ?? '') }}"
             readonly
             aria-label="Monto total calculado" aria-readonly="true">
    </div>
  </div>

  <div class="col-6 col-md-3">
    <label for="valor_cuotap{{ $idSuffix }}" class="font-weight-bold">
      <i class="fas fa-coins fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Valor por cuota
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="valor_cuota" id="valor_cuotap{{ $idSuffix }}"
             class="form-control bg-light font-weight-bold prestamo-valor-cuotap"
             style="font-size:1.15rem"
             value="{{ old('valor_cuota', $data->valor_cuota ?? '') }}"
             readonly
             aria-label="Valor de cada cuota calculado" aria-readonly="true">
    </div>
  </div>

  <div class="col-6 col-md-3">
    <label for="fecha_inicial{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-calendar-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Fecha inicial <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="date" name="fecha_inicial" id="fecha_inicial{{ $idSuffix }}"
           class="form-control form-control-sm"
           value="{{ old('fecha_inicial', $data->fecha_inicial ?? '') }}"
           required aria-required="true"
           aria-label="Fecha de inicio del préstamo">
  </div>

  <div class="col-6 col-md-3">
    <label for="usuario_idp{{ $idSuffix }}" class="font-weight-bold requerido">
      <i class="fas fa-user-tie fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Usuario <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select id="usuario_idp_display{{ $idSuffix }}"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            disabled aria-required="true"
            aria-label="Usuario responsable del préstamo">
      <option value="">— Seleccione el usuario —</option>
      @foreach ($usuarioscp as $id => $usuario)
        <option value="{{ $id }}" selected>{{ $usuario }}</option>
      @endforeach
    </select>
    <input type="hidden" name="usuario_id" id="usuario_idp{{ $idSuffix }}"
           value="{{ old('usuario_id', array_key_first($usuarioscp ?? []) ?? '') }}">
  </div>

</div>

{{-- ── Fila 3: observación ──────────────────────────────────── --}}
<div class="form-group row">
  <div class="col-12">
    <label for="observacion_prestamop{{ $idSuffix }}" class="font-weight-bold">
      <i class="fas fa-comment-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Observación
    </label>
    <textarea name="observacion_prestamo" id="observacion_prestamop{{ $idSuffix }}"
              class="form-control form-control-sm"
              rows="2"
              placeholder="Observación opcional del préstamo..."
              aria-label="Observación del préstamo"
              maxlength="100">{{ old('observacion_prestamo', $data->observacion_prestamo ?? '') }}</textarea>
  </div>
</div>

{{-- ── Fila 4: opciones de calendario ───────────────────────── --}}
<div class="form-group row">
  <div class="col-12">
    <label class="font-weight-bold d-block mb-1">
      <i class="fas fa-calendar-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Opciones de calendario
    </label>
    <div class="d-flex flex-wrap" style="gap:18px">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input"
               name="incluir_domingo" id="incluir_domingo{{ $idSuffix }}" value="1">
        <label class="custom-control-label" for="incluir_domingo{{ $idSuffix }}">
          Cobrar domingos
        </label>
      </div>
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input"
               name="incluir_festivo" id="incluir_festivo{{ $idSuffix }}" value="1">
        <label class="custom-control-label" for="incluir_festivo{{ $idSuffix }}">
          Cobrar feriados argentinos
        </label>
      </div>
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input prestamo-interes-prorrateado"
               id="interes_prorrateado{{ $idSuffix }}" value="1">
        <label class="custom-control-label" for="interes_prorrateado{{ $idSuffix }}">
          Prorratear interés mensual según frecuencia
        </label>
      </div>
    </div>
    <small class="text-muted mt-1 d-block">
      <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
      Por defecto se saltan domingos y feriados nacionales (Argentina).
      Actívalos solo si el acuerdo lo requiere.
    </small>
    <small class="text-muted d-block">
      <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
      La tasa de interés es mensual. Si activas el prorrateo, 6 cuotas quincenales
      o 12 semanales equivaldrán a 3 cuotas mensuales (mismo interés total).
    </small>
  </div>
</div>

{{-- Campos ocultos requeridos por el backend --}}
<input type="hidden" name="activo"          value="1">
<input type="hidden" name="estado"          value="C">
<input type="hidden" name="monto_pendiente" id="monto_pendientep{{ $idSuffix }}" class="prestamo-monto-pendientep" value="">

{{-- ── Nota informativa ─────────────────────────────────────── --}}
<div class="alert alert-light border-left border-info pl-3 py-2 mb-0" role="note">
  <small class="text-muted">
    <i class="fas fa-info-circle mr-1 text-info" aria-hidden="true"></i>
    El monto total y valor por cuota se calculan automáticamente al ingresar monto, cuotas e interés.
  </small>
</div>
