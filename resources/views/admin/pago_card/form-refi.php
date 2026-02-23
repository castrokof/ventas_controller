<div class="form-group row">
    
                    <div class="col-lg-6">
                        <input type="text" name="cliente" id="cliente_refi" class="form-control" value="" readonly >
                     </div>
                     <div class="col-lg-6">
                    </div>     
                        <label for="Monto" class="col-xs-8 control-label requerido">Monto pediente</label>
                         <input type="number" name="monto_pendiente_refi" id="monto_pendiente_refi" class="form-control" value="" readonly>
                     <div class="col-lg-6">
                            <label for="Monto a entregar" class="col-xs-8  control-label requerido">Monto a entregar</label>
                            <input type="number" name="monto_entregar" id="monto_entregar" class="form-control" value="" readonly >
                       </div>
                      <div class="col-lg-6">
                            <label for="fecha_inicial" class="col-xs-8 control-label requerido">fecha ini cobro</label>
                            <input type="date" name="fecha_inicial" id="fecha_inicial_refi" class="form-control" value="" required >
                      </div>                     
                      
           
         
 </div>          
<div class="form-group row">
  
    <div class="col-lg-3">
      
    <input type="hidden" name="prestamo_id" id="prestamo_id_refi" class="form-control" value="" readonly >
    <input type="hidden" name="cliente_id" id="cliente_id_refi" class="form-control" value="" readonly >
     <input type="hidden" name="usuario_id" id="usuario_idrefi" class="form-control" value="" readonly >
      <input type="hidden" name="monto" id="monto_refi" class="form-control" value="" readonly>
      <input type="hidden" name="valor_abono" id="valor_abono_refi" class="form-control" value="" readonly>
      <input type="hidden" name="abono" id="abono_refi" class="form-control" value="" readonly>
       <input type="hidden" name="interes" id="interes_refi" class="form-control" value="" readonly >
      <input type="hidden" name="cuotas" id="cuotas_refi" class="form-control" value="" readonly >
       <input type="hidden" name="tipo_pago" id="tipo_pagop_refi" class="form-control" value="" readonly >
        <input type="hidden" name="monto_total" id="monto_totalp_refi" class="form-control" value=""  readonly>
        <input type="hidden" name="valor_cuota" id="valor_cuotap_refi" class="form-control" value="" readonly >
        <input type="hidden" name="numero_cuota" id="numero_cuota_refi" class="form-control" value="" readonly >
        <input type="hidden" name="fecha_pago" id="fecha_pago_refi" class="form-control" value="" readonly >
        <input type="hidden" name="sync" id="sync_refi" class="form-control" value="" readonly >
         <input type="hidden" name="monto_pendiente" id="monto_pendiente_enviar" class="form-control" value=""  readonly>
        
        
        
         <input type="hidden" name="activo" id="activo_Refi" class="form-control" value="" readonly >
                 <input type="hidden" name="estado" id="estadop" class="form-control" value="C">
    </div>
    
</div>

<div class="form-group row">
 
        <div class="col-lg-6">
            <label for="observacion" class="col-xs-8 control-label requerido">Observación</label>
            <textarea name="observacion_prestamo" id="observacion_prestamo_refi" class="form-control" rows="3" placeholder="Enter ..." value="{{old('observacion', $data->observacion ?? '')}}"></textarea>
        </div>
        

</div>



