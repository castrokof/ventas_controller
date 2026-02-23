<div class="form-group row col-lg-12">
    <div class="col-md-6 col-12">
        <label for="fechadegestion" class="col-xs-2 control-label requerido">Fecha de Informes</label>
        <div class="form-group row">
              <input type="date" name="fechaini" id="fechaini"  class="form-control col-md-6 col-6 " value="">
              <input type="date" name="fechafin" id="fechafin" class="form-control col-md-6 col-6" value="">
        </div>
    </div>

    <div class="col-md-3 " id="usuario_info">
        <label for="usuario" class="col-xs-2 control-label">Usuario</label>
        <select name="usuario" id="usuario" class="form-control select2bs4" style="width: 100%;">
            <option value="">---seleccione el usuario---</option>
            @foreach ($usuarios as $usuario => $nombre))
            <option value="{{$usuario}}">{{$nombre}}</option>
            @endforeach
        </select>
    </div> 
    <div class="col-md-3">    
        <label>&nbsp;</label>
        <div class="form-group row">
            <button type="submit" name="reset" id="reset"  class="btn btn-default btn-xl col-md-6 col-6">Limpiar</button>
            <button type="submit" name="buscar" id="buscar" class="btn btn-success btn-xl col-md-6 col-6">Buscar</button>
        </div>    
</div>

</div>
