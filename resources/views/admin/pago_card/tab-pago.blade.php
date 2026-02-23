<div class="card-header bg-success with-border">
          <div class="col-lg-6 card-tools pull-right">
          <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-xs-12">
              <label for="estado" class="col-xs-4 control-label requerido">Seleccione los pagos</label>
                          <select name="estado_pago" id="estado_pago" class="form-control select2bs4" style="width: 80%;" required>
                          <option value="">---seleccione los pagos---</option>
                          <option value="0">Pagos por cobrar del día</option>
                          <option value="1">Pagos registrados del día</option>
                        <!--    <option value="2">Pagos atrasados y pendientes de cerrar</option>-->
                          <option value="4">Pagos por cobrar del día por prestamo</option>
                          <option value="5">Pagos registrados del día por prestamo</option>
                          </select>
            </div>
           </div>
          </div>
    </div>
      <div class="card-body table-responsive p-2">
        
      <table id="pago" class="table table-hover table-sm " cellspacing="0" width="100%">
       <thead>
        <tr>  
              <th>Acciones</th>
              <th>Datos</th>
              <th>Orden</th>
              
        </tr>
        </thead>
        <tbody>
           
        </tbody>
      </table>
    </div>
 
