<div class="form-group">
    {{ Form::label('name','Nombre de la categoría *') }}
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('type','Tipo') }}
    {{ Form::select('type', ['Positivo' => 'Positivo', 'Negativo' => 'Negativo'], null, ['placeholder' => 'Seleccione...','class' => 'form-control','required']) }}
</div>
<div class="form-group">
    {{ Form::submit('Guardar',['class' => 'btn btn-sm btn-success']) }}
</div>
<script>
    $(document).ready(function(){
        $("#name").on('keypress', function(e) {
            let regex = new RegExp("^[a-zA-Z ]*$");
            let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
    });
</script>