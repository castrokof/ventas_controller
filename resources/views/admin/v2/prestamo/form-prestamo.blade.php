{{-- resources/views/admin/v2/prestamo/form-prestamo.blade.php --}}
{{-- IMPORTANTE: los name= NO cambian para compatibilidad con backend --}}

{{-- ── Fila 1: cliente + monto + tipo + cuotas + interés ──── --}}
<div class="form-group row">

  <div class="col-12 col-md-4">
    <label for="cliente_id_p" class="font-weight-bold requerido">
      <i class="fas fa-user fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cliente <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="cliente_id" id="cliente_id_p"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Seleccione el cliente por documento">
      <option value="">— Seleccione documento / cliente —</option>
      @foreach ($clientes as $cliente)
        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
          {{ $cliente->documento }} → {{ $cliente->nombres }} {{ $cliente->apellidos }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-6 col-md-2">
    <label for="monto_p" class="font-weight-bold requerido">
      <i class="fas fa-dollar-sign fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="number" name="monto" id="monto_p"
             class="form-control"
             value="{{ old('monto', $data->monto ?? '') }}"
             min="1" required aria-required="true"
             aria-label="Monto a prestar"
             placeholder="0">
    </div>
  </div>

  <div class="col-6 col-md-2">
    <label for="tipo_pago_p" class="font-weight-bold requerido">
      <i class="fas fa-clock fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Tipo de pago <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_pago" id="tipo_pago_p"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Frecuencia de los pagos">
      <option value="">— Seleccione —</option>
      <option value="Diario"    {{ old('tipo_pago') === 'Diario'    ? 'selected' : '' }}>Diario</option>
      <option value="Semanal"   {{ old('tipo_pago') === 'Semanal'   ? 'selected' : '' }}>Semanal</option>
      <option value="Quincenal" {{ old('tipo_pago') === 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
      <option value="Mensual"   {{ old('tipo_pago') === 'Mensual'   ? 'selected' : '' }}>Mensual</option>
    </select>
  </div>

  <div class="col-6 col-md-2">
    <label for="cuotas_p" class="font-weight-bold requerido">
      <i class="fas fa-list-ol fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Cuotas <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="number" name="cuotas" id="cuotas_p"
           class="form-control form-control-sm"
           value="{{ old('cuotas', $data->cuotas ?? '') }}"
           min="1" required aria-required="true"
           aria-label="Número de cuotas del préstamo"
           placeholder="Ej. 30">
  </div>

  <div class="col-6 col-md-2">
    <label for="interes_p" class="font-weight-bold requerido">
      <i class="fas fa-percentage fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Interés <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <input type="number" name="interes" id="interes_p"
             class="form-control"
             value="{{ old('interes', $data->interes ?? '') }}"
             min="0" step="any"
             required aria-required="true"
             aria-label="Porcentaje de interés"
             placeholder="Ej. 20">
      <div class="input-group-append">
        <span class="input-group-text" aria-hidden="true">%</span>
      </div>
    </div>
  </div>

</div>

{{-- ── Fila 2: totales calculados ──────────────────────────── --}}
<div class="form-group row">

  <div class="col-6 col-md-3">
    <label for="monto_total_p" class="font-weight-bold">
      <i class="fas fa-calculator fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Monto total
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="monto_total" id="monto_total_p"
             class="form-control bg-light"
             value="{{ old('monto_total', $data->monto_total ?? '') }}"
             readonly aria-readonly="true"
             aria-label="Monto total calculado automáticamente">
    </div>
    <small class="text-muted">Se calcula automáticamente</small>
  </div>

  <div class="col-6 col-md-3">
    <label for="valor_cuota_p" class="font-weight-bold">
      <i class="fas fa-coins fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Valor por cuota
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="text" name="valor_cuota" id="valor_cuota_p"
             class="form-control bg-light"
             value="{{ old('valor_cuota', $data->valor_cuota ?? '') }}"
             readonly aria-readonly="true"
             aria-label="Valor de cada cuota calculado automáticamente">
    </div>
  </div>

  <div class="col-6 col-md-3">
    <label for="fecha_inicial_p" class="font-weight-bold requerido">
      <i class="fas fa-calendar-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Fecha inicial <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="date" name="fecha_inicial" id="fecha_inicial_p"
           class="form-control form-control-sm"
           value="{{ old('fecha_inicial', $data->fecha_inicial ?? '') }}"
           required aria-required="true"
           aria-label="Fecha de inicio del préstamo">
  </div>

  <div class="col-6 col-md-3">
    <label for="usuario_id_p" class="font-weight-bold requerido">
      <i class="fas fa-user-tie fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Usuario <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="usuario_id" id="usuario_id_p"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Usuario responsable del préstamo">
      <option value="">— Seleccione el usuario —</option>
      @foreach ($usuarios as $id => $usuario)
        <option value="{{ $id }}" selected>{{ $usuario }}</option>
      @endforeach
    </select>
  </div>

</div>

{{-- ── Fila 3: estado + observación + campos ocultos ──────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="activo_p" class="font-weight-bold requerido">
      <i class="fas fa-toggle-on fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Estado <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="activo" id="activo_p"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Estado del préstamo">
      <option value="">— Seleccione —</option>
      <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
      <option value="0" {{ old('activo') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <label for="observacion_prestamo_p" class="font-weight-bold">
      <i class="fas fa-comment-alt fa-xs mr-1 text-muted" aria-hidden="true"></i>
      Observación
    </label>
    <textarea name="observacion_prestamo" id="observacion_prestamo_p"
              class="form-control form-control-sm"
              rows="2"
              placeholder="Observación opcional del préstamo..."
              aria-label="Observación del préstamo"
              maxlength="100">{{ old('observacion_prestamo', $data->observacion_prestamo ?? '') }}</textarea>
  </div>

  {{-- Campos ocultos necesarios para el backend --}}
  <input type="hidden" name="estado"           value="{{ old('estado', 'C') }}">
  <input type="hidden" name="monto_pendiente"  id="monto_pendiente_p"
         value="{{ old('monto_pendiente', $data->monto_pendiente ?? '') }}">

</div>

{{-- ── Nota informativa ─────────────────────────────────────── --}}
<div class="alert alert-light border-left border-success pl-3 py-2 mb-0" role="note">
  <small class="text-muted">
    <i class="fas fa-info-circle mr-1 text-success" aria-hidden="true"></i>
    El <strong>monto total</strong> y el <strong>valor por cuota</strong> se calculan
    automáticamente al ingresar monto, cuotas e interés.
    El campo <em>usuario</em> está predefinido con tu sesión activa.
  </small>
</div>
