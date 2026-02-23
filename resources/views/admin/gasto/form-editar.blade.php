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
        <label for="tipodedocumento" class="col-xs-4 control-label requerido">Tipo de documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-control select2bs4" style="width: 100%;" required>
            <option value="">---seleccione---</option>
            <option value="DNI" {{($data->tipo_documento === "DNI") ?  "selected" : ""}}>DNI</option>
            <option value="CC" {{($data->tipo_documento === "CC") ?  "selected" : ""}}>CC</option>
            <option value="PASAPORTE" {{($data->tipo_documento === "PASAPORTE") ?  "selected" : ""}}>PASAPORTE</option>
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
    <label for="ciudad" class="col-xs-4 control-label requerido">Ciudad-Provincia</label>
    <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{old('ciudad', $data->ciudad ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="barrio" class="col-xs-4 control-label requerido">Barrio</label>
        <input type="text" name="barrio" id="barrio" class="form-control" value="{{old('barrio', $data->barrio ?? '')}}" required >
    </div>
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
        <label for="rol_id" class="col-xs-4 control-label requerido">Empresa</label>
                        <select name="empresa_id" id="empresa_id" class="form-control select2bs4" style="width: 100%;">
                        <option value="">---seleccione la empresa---</option>
                        @foreach ($empresa as $empresas => $id)
                        <option value="{{$id}}"{{old('empresa_id', $data->empresa_id ?? "") === $id ? "selected" : ""}}>{{$id.'=>'.$empresas}}</option>
                        @endforeach
                        </select>   
    </div>

    <div class="col-lg-3">
        <label for="estado" class="col-xs-4 control-label requerido">Estado</label>
                    <select name="activo" id="activo" class="form-control select2bs4" style="width: 100%;" required>
                    <option value="">---seleccione el estado---</option>
                    <option value="1" {{($data->activo === "1") ?  "selected" : ""}}>activo</option>
                    <option value="0" {{($data->activo === "0") ?  "selected" : ""}}>inactivo</option>
                    </select>
    </div>


   

</div>
