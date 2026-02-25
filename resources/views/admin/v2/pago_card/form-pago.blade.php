{{-- resources/views/admin/v2/pago_card/form-pago.blade.php --}}
{{-- IMPORTANTE: los name= de inputs NO cambian para compatibilidad con backend --}}

{{-- ── Fila 1: info del cliente y cuota ───────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="nombres" class="font-weight-bold">
      <i class="fas fa-user fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Nombres
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           id="nombres" value=""
           aria-label="Nombre del cliente" aria-readonly="true">
  </div>

  <div class="col-6 col-md-3">
    <label for="tipo_pago" class="font-weight-bold">
      <i class="fas fa-tag fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Tipo de venta
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           id="tipo_pago" value=""
           aria-label="Tipo de venta" aria-readonly="true">
  </div>

  <div class="col-6 col-md-2">
    <label for="idp" class="font-weight-bold">
      <i class="fas fa-hashtag fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Crédito #
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           name="prestamo_id" id="idp" value=""
           aria-label="Número de crédito" aria-readonly="true">
  </div>

  <div class="col-6 col-md-2">
    <label for="fecha_cuota" class="font-weight-bold">
      <i class="fas fa-calendar fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Fecha cuota
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           name="fecha_pago" id="fecha_cuota" value=""
           aria-label="Fecha de la cuota" aria-readonly="true">
  </div>

  <div class="col-6 col-md-2">
    <label for="n_cuota" class="font-weight-bold">
      <i class="fas fa-list-ol fa-xs mr-1 text-muted" aria-hidden="true"></i>
      # Cuota
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           name="numero_cuota" id="n_cuota"
           aria-label="Número de cuota" aria-readonly="true">
  </div>

</div>

{{-- ── Fila 2: valor cuota ─────────────────────────────────── --}}
<div class="form-group row">

  <div class="col-6 col-md-2">
    <label for="valor_cuota" class="font-weight-bold">
      <i class="fas fa-coins fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Valor cuota
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" readonly class="form-control bg-light"
             id="valor_cuota" name="valor_cuota"
             aria-label="Valor de la cuota" aria-readonly="true">
    </div>
  </div>

</div>

<hr class="my-2">

{{-- ── Fila 3: abono + estado + fecha opcional ─────────────── --}}
<div class="form-group row align-items-end">

  {{-- Valor abono --}}
  <div class="col-12 col-md-3" id="valor_abono_ocultar">
    <label for="valor_abono" class="font-weight-bold requerido">
      <i class="fas fa-hand-holding-usd fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Valor de abono <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="number" step="any" min="0"
             class="form-control"
             name="valor_abono" id="valor_abono"
             required
             aria-required="true"
             aria-label="Monto a abonar"
             aria-describedby="valor_abono_feedback">
    </div>
    <small id="valor_abono_feedback" class="field-feedback" role="alert"></small>
  </div>

  {{-- Estado cuota --}}
  <div class="col-6 col-md-2">
    <label for="estado_cuota" class="font-weight-bold">
      <i class="fas fa-traffic-light fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Estado cuota
    </label>
    <input type="text" readonly class="form-control form-control-sm bg-light"
           id="estado_cuota" name="estado_cuota"
           aria-label="Estado de la cuota" aria-readonly="true">
  </div>

  {{-- Switch cambiar fecha --}}
  <div class="col-6 col-md-2" id="checkbox1">
    <label class="font-weight-bold d-block">
      <i class="fas fa-calendar-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cambiar fecha
    </label>
    <div class="custom-control custom-switch">
      <input type="checkbox" class="custom-control-input"
             name="customSwitch1" id="customSwitch1"
             aria-label="Activar cambio de fecha de cuota">
      <label class="custom-control-label" for="customSwitch1">
        <span class="sr-only">Activar para cambiar la fecha de cuota</span>
      </label>
    </div>
  </div>

  {{-- Input nueva fecha (oculto por defecto) --}}
  <div class="col-6 col-md-2" id="chance_fecha" style="display:none">
    <label for="new_date" class="font-weight-bold">
      <i class="fas fa-calendar-check fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Nueva fecha
    </label>
    <input type="date" class="form-control form-control-sm"
           name="new_date_fecha" id="new_date"
           aria-label="Nueva fecha para la cuota">
  </div>

  {{-- Valor atrasado --}}
  <div class="col-6 col-md-2">
    <label for="vatraso" class="font-weight-bold">
      <i class="fas fa-exclamation-triangle fa-xs mr-1 text-danger" aria-hidden="true"></i>
      Valor atrasado
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text text-danger" aria-hidden="true">$</span>
      </div>
      <input type="text" readonly class="form-control bg-light"
             name="vatraso" id="vatraso"
             aria-label="Valor de cuota atrasada" aria-readonly="true">
    </div>
  </div>

</div>

{{-- ── Fila 4: observación ─────────────────────────────────── --}}
<div class="form-group row">
  <div class="col-12 col-md-6">
    <label for="observacion" class="font-weight-bold">
      <i class="fas fa-comment-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Observación
    </label>
    <input type="text" class="form-control form-control-sm"
           id="observacion" name="observacion"
           placeholder="Observación opcional"
           aria-label="Observación del pago"
           maxlength="200">
  </div>
</div>
