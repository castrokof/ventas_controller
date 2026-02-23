<div class="form-group row">
    
    <div class="col-lg-3">
        <label for="monto" class="col-xs-4 control-label requerido">Monto</label>
        <input type="text" name="monto" id="monto" class="form-control" value="{{old('monto', $data->monto ?? '')}}" required >
    </div>
    <div class="col-lg-6">
        <label for="descripcion" class="col-xs-8 control-label requerido">Observación</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" placeholder="Enter ..." value="{{old('descripcion', $data->observacion ?? '')}}"></textarea>
    </div>
    
    <div class="col-lg-3">
        
            <input type="hidden" name="usuario_id" id="usuario_id" class="form-control" value="{{Session()->get('usuario_id')}}" >
        
    </div>
    
</div>


</div>
