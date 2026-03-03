{{-- resources/views/admin/v2/gasto/form.blade.php --}}

<div class="form-group row">

  <div class="col-12 col-sm-5">
    <label for="monto_gasto" class="font-weight-bold requerido">
      Monto <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" aria-hidden="true">$</span>
      </div>
      <input type="number" name="monto" id="monto_gasto"
             class="form-control"
             value="{{ old('monto', $data->monto ?? '') }}"
             required aria-required="true"
             min="1" max="9999999999"
             inputmode="numeric"
             placeholder="Ej. 50000">
    </div>
  </div>

  <div class="col-12 mt-2">
    <label for="descripcion_gasto" class="font-weight-bold requerido">
      Descripción <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <textarea name="descripcion" id="descripcion_gasto"
              class="form-control" rows="4"
              required aria-required="true" maxlength="150"
              placeholder="Describe el concepto del gasto...">{{ old('descripcion', $data->descripcion ?? '') }}</textarea>
    <small class="form-text text-muted">
      <span id="char-count">0</span>/150 caracteres
    </small>
  </div>

  <input type="hidden" name="usuario_id" id="usuario_id_gasto"
         value="{{ Session()->get('usuario_id') }}">

</div>

<script>
(function(){
    var ta = document.getElementById('descripcion_gasto');
    var counter = document.getElementById('char-count');
    if (ta && counter) {
        counter.textContent = ta.value.length;
        ta.addEventListener('input', function(){ counter.textContent = this.value.length; });
    }
})();
</script>
