<div class="modal-body main-body">
    <div class="form-group row">
    <div class="col-lg-3">
        <label for="name">Nombres:</label>
        <input type="text" readonly class="form-control" id="nombres" value="">
    </div>
    <div class="col-lg-3">
        <label for="name">Tipo de Venta:</label>
        <input type="text" readonly class="form-control" id="tipo_pago" value="">
    </div>
    <div class="col-lg-1">
        <label for="idp">Credito#:</label>
        <input type="text" readonly class="form-control" name="prestamo_id" id="idp" value="">
    </div>
    <div class="col-lg-2">
        <label for="idp">Fecha de cuota:</label>
        
        <input type="text" readonly class="form-control" name="fecha_pago" id="fecha_cuota" value="">
    </div>
    <div class="col-lg-3">
        <label for="amount_value">Valor de venta:</label>
        <input type="text" readonly class="form-control" name="monto" id="monto">
    </div>
</div>
<div class="form-group row">
   
   <div class="col-lg-2">
        <label for="done">Saldo:</label>
        <input type="text" readonly class="form-control" name="monto_pendiente" id="monto_pendiente">
   </div>
   <div class="col-lg-2">
    <label for="done">Saldo total atrasado:</label>
    <input type="text" readonly class="form-control" name="monto_atrasado" id="monto_atrasado">
    </div>
    <div class="col-lg-2">
    <label for="done_payment">Cuotas atrasadas:</label>
    <input type="text" readonly class="form-control" id="cuotas_atrasadas">
    </div>
   <div class="col-lg-2">
        <label for="saldo">Cuota a pagar:</label>
        <input type="text" readonly class="form-control" name="numero_cuota" id="n_cuota">
    </div>
   <div class="col-lg-2">
        <label for="payment_number">Valor de cuota:</label>
        <input type="text" readonly class="form-control" id="valor_cuota" name="valor_cuota">
    </div>
    
   <div class="col-lg-2">
        <label for="done_payment">Cuotas pactadas:</label>
        <input type="text" readonly class="form-control" id="cuotas">
    </div>
</div>
      <div class="form-group row">
        <div id="valor_abono_ocultar" class="col-lg-4" style="display: block;">   
        <label for="amount">Valor de abono:</label>
        <input type="number" step="any" min="0" max="" required name="valor_abono" class="form-control" id="valor_abono">
        </div>
        <div class="col-lg-2" >   
        <label for="amount">Estado Cuota:</label>
       <input type="text" readonly class="form-control" id="estado_cuota" name="estado_cuota">
        </div>
        
        <div id="checkbox1" class="col-lg-3">
             <label for="amount">Cambiar fecha de cuota:</label>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="customSwitch1" id="customSwitch1"> 
                <label class="custom-control-label" for="customSwitch1"></label>
              </div>
          </div>
          <div id="chance_fecha" style="display: none;" class="col-lg-3">
            <label for="fecha">Cambio de fecha:</label>
            <input type="date" class="form-control" name="new_date_fecha" id="new_date"> 
         </div> 
          <div class="col-lg-2">
            <label for="done">Valor atrasado de cuota:</label>
            <input type="text" readonly class="form-control" name="vatraso" id="vatraso">
         </div>
        
    </div>
</div>
<div class="col-lg-12">
    <label for="observacion" class="col-xs-8 control-label requerido">Observación</label>
    <textarea name="observacion_pago" id="observacion_pago" class="form-control" rows="3" placeholder="Enter ..." value="{{old('observacion')}}"></textarea>
    <input type="hidden" name="sync" id="sync" value="1">
</div>
<input type="hidden" name="abono" value="1"> 

