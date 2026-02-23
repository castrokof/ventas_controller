<div class="form-group row">
    <div class="col-lg-3">
        <label for="nombres" class="col-xs-4 control-label requerido">Nombres</label>
        <input type="text" name="nombres" id="nombres" class="form-control" value="{{old('nombres', $data->nombres ?? '')}}" required >
    </div>
    <div class="col-lg-3">
    <label for="apellidos" class="col-xs-4 control-label requerido">Apellidos</label>
    <input type="text" name="apellidos" id="apellidos" class="form-control" value="{{old('apellidos', $data->apellidos ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="tipo_documento" class="col-xs-4 control-label requerido">Tipo de documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-control select2bs4" style="width: 100%;" required>
            <option value="">---seleccione---</option>
            <option value="DNI">DNI</option>
            <option value="CC">CC</option>
            <option value="PASAPORTE">PASAPORTE</option>
            </select>
    </div>
    <div class="col-lg-3">
        <label for="documento" class="col-xs-4 control-label requerido">Documento</label>
        <input type="text" name="documento" id="documento" class="form-control" value="{{old('documento', $data->documento ?? '')}}" minlength="6"  required >
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label for="pais" class="col-xs-4 control-label requerido">Pais</label>
        <input type="text" name="pais" id="pais" class="form-control" value="{{old('pais', $data->pais ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="estado" class="col-xs-4 control-label requerido">estado</label>
        <input type="text" name="estado" id="estado" class="form-control" value="{{old('estado', $data->estado ?? '')}}" required >
    </div>
    <div class="col-lg-2">
    <label for="ciudad" class="col-xs-4 control-label requerido">Ciudad-Provincia</label>
    <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{old('ciudad', $data->ciudad ?? '')}}" required >
    </div>
    <div class="col-lg-2">
        <label for="barrio" class="col-xs-4 control-label requerido">Barrio</label>
        <input type="text" name="barrio" id="barrio" class="form-control" value="{{old('barrio', $data->barrio ?? '')}}" required >
    </div>
    <div class="col-lg-2">
        <label for="sector" class="col-xs-4 control-label requerido">Sector</label>
        <input type="text" name="sector" id="sector" class="form-control" value="{{old('sector', $data->sector ?? '')}}"  >
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-3">
        <label for="direccion" class="col-xs-4 control-label requerido">Direccion</label>
        <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->direccion ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="celular" class="col-xs-4 control-label requerido">Celular</label>
        <input type="text" name="celular" id="celular" class="form-control" value="{{old('celular', $data->celular ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="telefono" class="col-xs-4 control-label requerido">Telefono</label>
        <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="rol_id" class="col-xs-4 control-label requerido">Usuario</label>
                        <select name="usuario_id" id="usuario_id" class="form-control select2bs4" style="width: 100%;" required>
                        <option value="">---seleccione el usuario---</option>
                        @foreach ($usuarios as $id => $usuario)
                        <option value="{{$id}}" {{old('usuario_id', $datas->usuario_id ?? "") == $id ? "selected" : "selected"}} >{{$usuario}}</option>
                        @endforeach
                        </select>
    </div>
</div>
<div class="form-group row">
        <div class="col-lg-3">
            <label for="consecutivo" class="col-xs-4 control-label requerido">Consecutivo</label>
            <input type="number" name="consecutivo" id="consecutivo" class="form-control" value="{{old('consecutivo', $data->consecutivo ?? '')}}" required >
        </div>
    
        <div class="col-lg-3">
        <label for="estado" class="col-xs-4 control-label requerido">Estado</label>
                    <select name="activo" id="activo" class="form-control select2bs4" style="width: 100%;" required>
                    <option value="">---seleccione el estado---</option>
                    <option value="1">activo</option>
                    <option value="0">inactivo</option>
                    </select>
        </div>
        <div class="col-lg-6">
            <label for="observacion" class="col-xs-8 control-label requerido">Observación</label>
            <textarea name="observacion" id="observacion" class="form-control" rows="3" placeholder="Enter ..." value="{{old('observacion', $data->observacion ?? '')}}"></textarea>
        </div>
</div>



