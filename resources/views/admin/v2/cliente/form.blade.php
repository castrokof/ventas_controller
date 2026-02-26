{{-- resources/views/admin/v2/cliente/form.blade.php --}}
{{-- IMPORTANTE: los name= NO cambian para compatibilidad con backend --}}

{{-- ── Fila 1: identificación ──────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="nombrescli" class="font-weight-bold requerido">
      Nombres <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="nombres" id="nombrescli"
           class="form-control form-control-sm"
           value="{{ old('nombres', $data->nombres ?? '') }}"
           required aria-required="true"
           aria-label="Nombres del cliente"
           placeholder="Ej. Juan Carlos">
  </div>

  <div class="col-12 col-md-3">
    <label for="apellidoscli" class="font-weight-bold requerido">
      Apellidos <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="apellidos" id="apellidoscli"
           class="form-control form-control-sm"
           value="{{ old('apellidos', $data->apellidos ?? '') }}"
           required aria-required="true"
           aria-label="Apellidos del cliente"
           placeholder="Ej. Pérez García">
  </div>

  <div class="col-12 col-md-3">
    <label for="tipo_documentocli" class="font-weight-bold requerido">
      Tipo documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_documento" id="tipo_documentocli"
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
    <label for="documentocli" class="font-weight-bold requerido">
      Documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="documento" id="documentocli"
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
    <label for="paiscli" class="font-weight-bold requerido">
      País <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="pais" id="paiscli"
           class="form-control form-control-sm"
           value="{{ old('pais', $data->pais ?? '') }}"
           required aria-required="true"
           aria-label="País de residencia"
           placeholder="Ej. Colombia">
  </div>

  <div class="col-12 col-md-3">
    <label for="estadocli" class="font-weight-bold requerido">
      Estado / Dpto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="estado" id="estadocli"
           class="form-control form-control-sm"
           value="{{ old('estado', $data->estado ?? '') }}"
           required aria-required="true"
           aria-label="Estado o departamento"
           placeholder="Ej. Antioquia">
  </div>

  <div class="col-12 col-md-2">
    <label for="ciudadcli" class="font-weight-bold requerido">
      Ciudad <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="ciudad" id="ciudadcli"
           class="form-control form-control-sm"
           value="{{ old('ciudad', $data->ciudad ?? '') }}"
           required aria-required="true"
           aria-label="Ciudad o provincia"
           placeholder="Ej. Medellín">
  </div>

  <div class="col-12 col-md-2">
    <label for="barriocli" class="font-weight-bold requerido">
      Barrio <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="barrio" id="barriocli"
           class="form-control form-control-sm"
           value="{{ old('barrio', $data->barrio ?? '') }}"
           required aria-required="true"
           aria-label="Barrio del cliente"
           placeholder="Ej. El Poblado">
  </div>

  <div class="col-12 col-md-2">
    <label for="sectorcli" class="font-weight-bold">Sector</label>
    <input type="text" name="sector" id="sectorcli"
           class="form-control form-control-sm"
           value="{{ old('sector', $data->sector ?? '') }}"
           aria-label="Sector del cliente"
           placeholder="Opcional">
  </div>

</div>

{{-- ── Fila 3: contacto ────────────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="direccioncli" class="font-weight-bold requerido">
      Dirección <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="direccion" id="direccioncli"
           class="form-control form-control-sm"
           value="{{ old('direccion', $data->direccion ?? '') }}"
           required aria-required="true"
           aria-label="Dirección del cliente"
           placeholder="Ej. Cra 50 # 30-20">
  </div>

  <div class="col-12 col-md-3">
    <label for="celularcli" class="font-weight-bold requerido">
      Celular <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">
          <i class="fas fa-mobile-alt"></i>
        </span>
      </div>
      <input type="text" name="celular" id="celularcli"
             class="form-control"
             value="{{ old('celular', $data->celular ?? '') }}"
             required aria-required="true"
             aria-label="Número de celular"
             placeholder="Ej. 3001234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="telefonocli" class="font-weight-bold requerido">
      Teléfono <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">
          <i class="fas fa-phone"></i>
        </span>
      </div>
      <input type="text" name="telefono" id="telefonocli"
             class="form-control"
             value="{{ old('telefono', $data->telefono ?? '') }}"
             required aria-required="true"
             aria-label="Número de teléfono fijo"
             placeholder="Ej. 6041234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="usuario_id_cli" class="font-weight-bold requerido">
      Usuario <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="usuario_id" id="usuario_id_cli"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Usuario propietario del cliente"
            readonly>
      <option value="">— Seleccione —</option>
      @foreach ($usuarios as $uid => $uname)
        <option value="{{ $uid }}"
          {{ old('usuario_id', $datas->usuario_id ?? $uid) == $uid ? 'selected' : '' }}>
          {{ $uname }}
        </option>
      @endforeach
    </select>
  </div>

</div>

{{-- ── Fila 4: datos adicionales ───────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="consecutivocli" class="font-weight-bold requerido">
      Consecutivo <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="number" name="consecutivo" id="consecutivocli"
           class="form-control form-control-sm"
           value="{{ old('consecutivo', $data->consecutivo ?? '') }}"
           required aria-required="true" min="1"
           aria-label="Número consecutivo del cliente"
           placeholder="Ej. 1">
  </div>

  <div class="col-12 col-md-3">
    <label for="activocli" class="font-weight-bold requerido">
      Estado <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="activo" id="activocli"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Estado activo o inactivo del cliente">
      <option value="">— Seleccione —</option>
      <option value="1" {{ old('activo', $data->activo ?? '') == 1 ? 'selected' : '' }}>Activo</option>
      <option value="0" {{ old('activo', $data->activo ?? '') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <label for="observacioncli" class="font-weight-bold">Observación</label>
    <textarea name="observacion_cli" id="observacioncli"
              class="form-control form-control-sm"
              rows="2"
              aria-label="Observaciones sobre el cliente"
              placeholder="Opcional...">{{ old('observacion_cli', $data->observacion_cli ?? '') }}</textarea>
  </div>

</div>
