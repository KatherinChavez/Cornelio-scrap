<div class="form-group">
    {{ Form::label('word','Palabra') }}
    {{ Form::text('word',null,['class' => 'form-control','required']) }}
</div>
<div class="form-group">
    {{ Form::label('description','Descripción') }}
    {{ Form::textarea('description',null,['class' => 'form-control']) }}
</div>
<div class="form-group">
    {{ Form::label('subcategory_id','Subategoría') }}
    {{ Form::select('subcategory_id',$subcategories,null,['class'=>'form-control','placeholder'=>'Seleccione subcategoría','required']) }}
</div>
<div class="form-group">
    {{ Form::label('priority','Prioridad') }}
    {{ Form::select('priority',['1'=>'Baja','2'=>'Media','3'=>'Alta'],'0',['class'=>'form-control','placeholder'=>'Seleccione la prioridad','required']) }}
</div>
<div class="form-group">
    {{ Form::submit('Guardar',['class' => 'btn btn-sm btn-primary']) }}
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