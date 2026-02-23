<div class="form-group row">
    <div class="col-lg-3">
        <label for="cliente_id" class="col-xs-4 control-label requerido">cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-control select2bs4" style="width: 100%;" required>
            <option value="">---seleccione documento---</option>
            @foreach ($clientes as $id =>$cliente)
                        <option value="{{$cliente->id}}" {{old('cliente_id')}} >{{$cliente->documento.'=>'.$cliente->nombres.' '.$cliente->apellidos}}</option>
            @endforeach
            </select>   
    </div>
    <div class="col-lg-3">
        <label for="Monto" class="col-xs-4 control-label requerido">Monto</label>
        <input type="number" name="monto" id="monto" class="form-control" value="{{old('monto', $data->monto ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="tipo_pago" class="col-xs-4 control-label requerido">Tipo de pago</label>
        <select name="tipo_pago" id="tipo_pago" class="form-control select2bs4" style="width: 100%;" required>
            <option value="">---seleccione---</option>
            <option value="Diario">Diario</option>
            <option value="Semanal">Semanal</option>
            <option value="Quincenal">Quincenal</option>
            <option value="Mensual">Mensual</option>
            </select>
    </div>
    <div class="col-lg-1">
        <label for="cuotas" class="col-xs-4 control-label requerido">Cuotas</label>
        <input type="text" name="cuotas" id="cuotas" class="form-control" value="{{old('cuotas', $data->cuotas ?? '')}}" required >
        </div>
        <div class="col-lg-2">
         <label for="cuotas" class="col-xs-4 control-label requerido">Interes</label>
        <input type="text" name="interes" id="interes" class="form-control" value="{{old('interes', $data->interes ?? '')}}" required >
        <!--<div class="col-lg-2">
            <label for="interes" class="col-xs-4 control-label requerido">Interes</label>
            <select name="interes" id="interes" class="form-control select2bs4" style="width: 100%;" required>
                <option value="">---seleccione---</option>
                <option value="0.1">10%</option>
                <option value="0.15">15%</option>
                <option value="0.2">20%</option>
                <option value="0.25">25%</option>
                <option value="0.3">30%</option>
                <option value="0.32">32%</option>
                <option value="0.35">35%</option>
                <option value="0.4">40%</option>
                <option value="0.45">45%</option>
                <option value="0.5">50%</option>
                </select>-->
        </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label for="monto_total" class="col-xs-4 control-label requerido">Monto Total</label>
        <input type="text" name="monto_total" id="monto_total" class="form-control" value="{{old('monto_total', $data->monto_total ?? '')}}"  readonly>
    </div>
    <div class="col-lg-3">
        <label for="valor_cuota" class="col-xs-4 control-label requerido">Valor Cuota</label>
        <input type="text" name="valor_cuota" id="valor_cuota" class="form-control" value="{{old('valor_cuota', $data->valor_cuota ?? '')}}" readonly >
    </div>
   
    <div class="col-lg-3">
        <label for="fecha_inicial" class="col-xs-4 control-label requerido">fecha_inicial</label>
        <input type="date" name="fecha_inicial" id="fecha_inicial" class="form-control" value="{{old('fecha_inicial', $data->fecha_inicial ?? '')}}" required >
    </div>
    <div class="col-lg-3">
        <label for="rol_id" class="col-xs-4 control-label requerido">Usuario</label>
                        <select name="usuario_id" id="usuario_id" class="form-control select2bs4" style="width: 100%;" readonly required>
                        <option value="">---seleccione el usuario---</option>
                        @foreach ($usuarios as $id => $usuario)
                        <option value="{{$id}}" {{old('usuario_id', $datas->usuario_id ?? "") == $id ? "selected" : "selected"}} >{{$usuario}}</option>
                        @endforeach
                        </select>
    </div>
    
</div>

<div class="form-group row">
 
   
        
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
            <textarea name="observacion_prestamo" id="observacion_prestamo" class="form-control" rows="3" placeholder="Enter ..." value="{{old('observacion', $data->observacion ?? '')}}"></textarea>
        </div>
        
        <input type="hidden" name="estado" id="estado" class="form-control" value="{{old('cliente_id', 'C' ?? '')}}">
        
        <input type="hidden" name="monto_pendiente" id="monto_pendiente" class="form-control" value="{{old('monto_pendiente', $data->monto_pendiente ?? '')}}">

</div>



