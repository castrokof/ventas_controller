{{-- resources/views/admin/v2/pago_card/form-prestamo.blade.php --}}
{{-- IMPORTANTE: los name= NO cambian para compatibilidad con backend --}}

{{-- ── Fila 1: cliente + monto + tipo + cuotas + interés ──── --}}
<div class="form-group row">

  <div class="col-12 col-md-4">
    <label for="cliente_id" class="font-weight-bold requerido">
      <i class="fas fa-user fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cliente <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="cliente_id" id="cliente_id"
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
    <label for="montop" class="font-weight-bold requerido">
      <i class="fas fa-dollar-sign fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="number" name="monto" id="montop"
             class="form-control"
             value="{{ old('monto', $data->monto ?? '') }}"
             required aria-required="true"
             aria-label="Monto del préstamo"
             placeholder="0">
    </div>
  </div>

  <div class="col-6 col-md-2">
    <label for="tipo_pagop" class="font-weight-bold requerido">
      <i class="fas fa-clock fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Tipo de pago <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_pago" id="tipo_pagop"
            class="form-control form-control-sm select2bs4"
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
    <label for="cuotas" class="font-weight-bold requerido">
      <i class="fas fa-list-ol fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cuotas <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="cuotas" id="cuotas"
           class="form-control form-control-sm"
           value="{{ old('cuotas', $data->cuotas ?? '') }}"
           required aria-required="true"
           aria-label="Número de cuotas"
           placeholder="Ej. 30">
  </div>

  <div class="col-6 col-md-2">
    <label for="interes" class="font-weight-bold requerido">
      <i class="fas fa-percentage fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Interés <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <input type="text" name="interes" id="interes"
             class="form-control"
             value="{{ old('interes', $data->interes ?? '') }}"
             required aria-required="true"
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
    <label for="monto_totalp" class="font-weight-bold">
      <i class="fas fa-calculator fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto total
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="monto_total" id="monto_totalp"
             class="form-control bg-light"
             value="{{ old('monto_total', $data->monto_total ?? '') }}"
             readonly
             aria-label="Monto total calculado" aria-readonly="true">
    </div>
  </div>

  <div class="col-6 col-md-3">
    <label for="valor_cuotap" class="font-weight-bold">
      <i class="fas fa-coins fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Valor por cuota
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="valor_cuota" id="valor_cuotap"
             class="form-control bg-light"
             value="{{ old('valor_cuota', $data->valor_cuota ?? '') }}"
             readonly
             aria-label="Valor de cada cuota calculado" aria-readonly="true">
    </div>
  </div>

  <div class="col-6 col-md-3">
    <label for="fecha_inicial" class="font-weight-bold requerido">
      <i class="fas fa-calendar-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Fecha inicial <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="date" name="fecha_inicial" id="fecha_inicial"
           class="form-control form-control-sm"
           value="{{ old('fecha_inicial', $data->fecha_inicial ?? '') }}"
           required aria-required="true"
           aria-label="Fecha de inicio del préstamo">
  </div>

  <div class="col-6 col-md-3">
    <label for="usuario_idp" class="font-weight-bold requerido">
      <i class="fas fa-user-tie fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Usuario <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="usuario_id" id="usuario_idp"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            readonly required aria-required="true"
            aria-label="Usuario responsable del préstamo">
      <option value="">— Seleccione el usuario —</option>
      @foreach ($usuarioscp as $id => $usuario)
        <option value="{{ $id }}" selected>{{ $usuario }}</option>
      @endforeach
    </select>
  </div>

</div>

{{-- ── Nota informativa ─────────────────────────────────────── --}}
<div class="alert alert-light border-left border-info pl-3 py-2 mb-0" role="note">
  <small class="text-muted">
    <i class="fas fa-info-circle mr-1 text-info" aria-hidden="true"></i>
    El monto total y valor por cuota se calculan automáticamente al ingresar monto, cuotas e interés.
  </small>
</div>
