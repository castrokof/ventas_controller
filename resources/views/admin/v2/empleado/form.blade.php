{{-- resources/views/admin/v2/empleado/form.blade.php --}}
{{-- Los name= NO cambian para mantener compatibilidad con el backend --}}

{{-- ── Fila 1: identificación ──────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="nombres_emp" class="font-weight-bold requerido">
      Nombres <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="nombres" id="nombres_emp"
           class="form-control form-control-sm"
           value="{{ old('nombres', $data->nombres ?? '') }}"
           required aria-required="true"
           aria-label="Nombres del empleado"
           placeholder="Ej. Juan Carlos">
  </div>

  <div class="col-12 col-md-3">
    <label for="apellidos_emp" class="font-weight-bold requerido">
      Apellidos <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="apellidos" id="apellidos_emp"
           class="form-control form-control-sm"
           value="{{ old('apellidos', $data->apellidos ?? '') }}"
           required aria-required="true"
           aria-label="Apellidos del empleado"
           placeholder="Ej. Pérez García">
  </div>

  <div class="col-12 col-md-3">
    <label for="tipo_documento_emp" class="font-weight-bold requerido">
      Tipo documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="tipo_documento" id="tipo_documento_emp"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Tipo de documento del empleado">
      <option value="">— Seleccione —</option>
      <option value="DNI">DNI</option>
      <option value="CC">CC</option>
      <option value="PASAPORTE">Pasaporte</option>
    </select>
  </div>

  <div class="col-12 col-md-3">
    <label for="documento_emp" class="font-weight-bold requerido">
      Documento <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="documento" id="documento_emp"
           class="form-control form-control-sm"
           value="{{ old('documento', $data->documento ?? '') }}"
           required aria-required="true" minlength="5"
           aria-label="Número de documento"
           placeholder="Ej. 12345678">
  </div>

</div>

{{-- ── Fila 2: ubicación ───────────────────────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="pais_emp" class="font-weight-bold requerido">
      País <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="pais" id="pais_emp"
           class="form-control form-control-sm"
           value="{{ old('pais', $data->pais ?? '') }}"
           required aria-required="true"
           aria-label="País del empleado"
           placeholder="Ej. Colombia">
  </div>

  <div class="col-12 col-md-3">
    <label for="ciudad_emp" class="font-weight-bold requerido">
      Ciudad <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="ciudad" id="ciudad_emp"
           class="form-control form-control-sm"
           value="{{ old('ciudad', $data->ciudad ?? '') }}"
           required aria-required="true"
           aria-label="Ciudad del empleado"
           placeholder="Ej. Medellín">
  </div>

  <div class="col-12 col-md-3">
    <label for="barrio_emp" class="font-weight-bold requerido">
      Barrio <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="barrio" id="barrio_emp"
           class="form-control form-control-sm"
           value="{{ old('barrio', $data->barrio ?? '') }}"
           required aria-required="true"
           aria-label="Barrio del empleado"
           placeholder="Ej. El Poblado">
  </div>

  <div class="col-12 col-md-3">
    <label for="direccion_emp" class="font-weight-bold requerido">
      Dirección <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input type="text" name="direccion" id="direccion_emp"
           class="form-control form-control-sm"
           value="{{ old('direccion', $data->direccion ?? '') }}"
           required aria-required="true"
           aria-label="Dirección del empleado"
           placeholder="Ej. Cra 50 # 30-20">
  </div>

</div>

{{-- ── Fila 3: contacto + empresa + estado ─────────────────── --}}
<div class="form-group row">

  <div class="col-12 col-md-3">
    <label for="celular_emp" class="font-weight-bold requerido">
      Celular <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true"><i class="fas fa-mobile-alt"></i></span>
      </div>
      <input type="text" name="celular" id="celular_emp"
             class="form-control"
             value="{{ old('celular', $data->celular ?? '') }}"
             aria-label="Número de celular del empleado"
             placeholder="Ej. 3001234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="telefono_emp" class="font-weight-bold">Teléfono</label>
    <div class="input-group input-group-sm">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true"><i class="fas fa-phone"></i></span>
      </div>
      <input type="text" name="telefono" id="telefono_emp"
             class="form-control"
             value="{{ old('telefono', $data->telefono ?? '') }}"
             aria-label="Teléfono fijo del empleado"
             placeholder="Ej. 6041234567">
    </div>
  </div>

  <div class="col-12 col-md-3">
    <label for="empresa_id_emp" class="font-weight-bold requerido">
      Empresa <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="empresa_id" id="empresa_id_emp"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Empresa a la que pertenece el empleado">
      <option value="">— Seleccione —</option>
      @foreach ($empresa as $nombre => $id)
        <option value="{{ $id }}"
          {{ old('empresa_id', $data->empresa_id ?? '') == $id ? 'selected' : '' }}>
          {{ $id }} &rarr; {{ $nombre }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-3">
    <label for="activo_emp" class="font-weight-bold requerido">
      Estado <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <select name="activo" id="activo_emp"
            class="form-control form-control-sm select2bs4"
            style="width:100%"
            required aria-required="true"
            aria-label="Estado activo/inactivo del empleado">
      <option value="">— Seleccione —</option>
      <option value="1" {{ old('activo', $data->activo ?? '') == 1 ? 'selected' : '' }}>Activo</option>
      <option value="0" {{ old('activo', $data->activo ?? '') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
  </div>

</div>
