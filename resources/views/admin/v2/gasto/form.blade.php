{{-- resources/views/admin/v2/gasto/form.blade.php --}}

{{-- ── Fila única: monto + descripción ────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-4">
    <label for="monto_gasto" class="font-weight-bold requerido">
      Monto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="number" name="monto" id="monto_gasto"
             class="form-control"
             value="{{ old('monto', $data->monto ?? '') }}"
             required aria-required="true"
             min="1" max="9999999999"
             aria-label="Monto del gasto"
             placeholder="Ej. 50000">
    </div>
  </div>

  <div class="col-12 col-md-8">
    <label for="descripcion_gasto" class="font-weight-bold requerido">
      Descripción <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <textarea name="descripcion" id="descripcion_gasto"
              class="form-control form-control-sm"
              rows="3"
              required aria-required="true"
              maxlength="150"
              aria-label="Descripción del gasto"
              placeholder="Describe el concepto del gasto...">{{ old('descripcion', $data->descripcion ?? '') }}</textarea>
    <small class="form-text text-muted">Máximo 150 caracteres.</small>
  </div>

  {{-- usuario_id oculto — se envía automáticamente desde sesión --}}
  <input type="hidden" name="usuario_id" id="usuario_id_gasto"
         value="{{ Session()->get('usuario_id') }}">

</div>
