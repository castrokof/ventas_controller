{{-- resources/views/admin/v2/pago_card/form-editar.blade.php --}}
{{-- IMPORTANTE: los name= NO cambian para compatibilidad con backend --}}

{{-- ── Fila 1: identificación ──────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="nombres" class="font-weight-bold requerido">
      Nombres <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="nombres" id="nombres"
           class="form-control form-control-sm"
           value="{{ old('nombres', $data->nombres ?? '') }}"
           required aria-required="true"
           aria-label="Nombres del cliente"
           placeholder="Ej. Juan Carlos">
  </div>

  <div class="col-12 col-md-3">
    <label for="apellidos" class="font-weight-bold requerido">
      Apellidos <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="apellidos" id="apellidos"
           class="form-control form-control-sm"
           value="{{ old('apellidos', $data->apellidos ?? '') }}"
           required aria-required="true"
           aria-label="Apellidos del cliente"
           placeholder="Ej. Pérez García">
  </div>

  <div class="col-12 col-md-3">
    <label for="tipo_documento" class="font-weight-bold requerido">
      Tipo documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_documento" id="tipo_documento"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Tipo de documento de identidad">
      <option value="">— Seleccione —</option>
      <option value="DNI">DNI</option>
      <option value="CC">CC</option>
      <option value="PASAPORTE">Pasaporte</option>
    </select>
  </div>

  <div class="col-12 col-md-3">
    <label for="documento" class="font-weight-bold requerido">
      Documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="documento" id="documento"
           class="form-control form-control-sm"
           value="{{ old('documento', $data->documento ?? '') }}"
           required aria-required="true" minlength="6"
           aria-label="Número de documento"
           placeholder="Ej. 12345678">
  </div>

</div>

{{-- ── Fila 2: ubicación ───────────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="pais" class="font-weight-bold requerido">
      País <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="pais" id="pais"
           class="form-control form-control-sm"
           value="{{ old('pais', $data->pais ?? '') }}"
           required aria-required="true"
           aria-label="País de residencia"
           placeholder="Ej. Colombia">
  </div>

  <div class="col-12 col-md-3">
    <label for="estado" class="font-weight-bold requerido">
      Estado / Dpto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="estado" id="estado"
           class="form-control form-control-sm"
           value="{{ old('estado', $data->estado ?? '') }}"
           required aria-required="true"
           aria-label="Estado o departamento"
           placeholder="Ej. Antioquia">
  </div>

  <div class="col-12 col-md-2">
    <label for="ciudad" class="font-weight-bold requerido">
      Ciudad <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="ciudad" id="ciudad"
           class="form-control form-control-sm"
           value="{{ old('ciudad', $data->ciudad ?? '') }}"
           required aria-required="true"
           aria-label="Ciudad o provincia"
           placeholder="Ej. Medellín">
  </div>

  <div class="col-12 col-md-2">
    <label for="barrio" class="font-weight-bold requerido">
      Barrio <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="barrio" id="barrio"
           class="form-control form-control-sm"
           value="{{ old('barrio', $data->barrio ?? '') }}"
           required aria-required="true"
           aria-label="Barrio del cliente"
           placeholder="Ej. El Poblado">
  </div>

  <div class="col-12 col-md-2">
    <label for="sector" class="font-weight-bold">Sector</label>
    <input type="text" name="sector" id="sector"
           class="form-control form-control-sm"
           value="{{ old('sector', $data->sector ?? '') }}"
           aria-label="Sector del cliente"
           placeholder="Opcional">
  </div>

</div>

{{-- ── Fila 3: contacto ────────────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="direccion" class="font-weight-bold requerido">
      Dirección <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="direccion" id="direccion"
           class="form-control form-control-sm"
           value="{{ old('direccion', $data->direccion ?? '') }}"
           required aria-required="true"
           aria-label="Dirección del cliente"
           placeholder="Ej. Cra 50 # 30-20">
  </div>

  <div class="col-12 col-md-3">
    <label for="celular" class="font-weight-bold requerido">
      Celular <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">
          <i class="fas fa-mobile-alt"></i>
        </span>
      </div>
      <input type="text" name="celular" id="celular"
             class="form-control"
             value="{{ old('celular', $data->celular ?? '') }}"
             required aria-required="true"
             aria-label="Número de celular"
             placeholder="Ej. 3001234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="telefono" class="font-weight-bold requerido">
      Teléfono <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">
          <i class="fas fa-phone"></i>
        </span>
      </div>
      <input type="text" name="telefono" id="telefono"
             class="form-control"
             value="{{ old('telefono', $data->telefono ?? '') }}"
             required aria-required="true"
             aria-label="Número de teléfono fijo"
             placeholder="Ej. 6041234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="usuario_id" class="font-weight-bold requerido">
      Usuario <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="usuario_id" id="usuario_id"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Usuario asignado">
      <option value="">— Seleccione el usuario —</option>
      @foreach ($usuarios as $id => $usuario)
        <option value="{{ $id }}"
          {{ old('usuario_id', $datas->usuario_id ?? '') == $id ? 'selected' : '' }}>
          {{ $usuario }}
        </option>
      @endforeach
    </select>
  </div>

</div>

{{-- ── Fila 4: consecutivo + estado + observación ──────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="consecutivo" class="font-weight-bold requerido">
      Consecutivo <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="number" name="consecutivo" id="consecutivo"
           class="form-control form-control-sm"
           value="{{ old('consecutivo', $data->consecutivo ?? '') }}"
           required aria-required="true"
           aria-label="Número consecutivo del cliente">
  </div>

  <div class="col-12 col-md-3">
    <label for="activo" class="font-weight-bold requerido">
      Estado <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="activo" id="activo"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Estado del cliente">
      <option value="">— Seleccione el estado —</option>
      <option value="1">Activo</option>
      <option value="0">Inactivo</option>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <label for="observacion" class="font-weight-bold">Observación</label>
    <textarea name="observacion" id="observacion"
              class="form-control form-control-sm"
              rows="2"
              placeholder="Observación del cliente..."
              aria-label="Observación del cliente">{{ old('observacion', $data->observacion ?? '') }}</textarea>
  </div>

</div>
