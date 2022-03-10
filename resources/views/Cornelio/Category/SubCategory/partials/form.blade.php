<div class="form-group">
    {{ Form::label('name','Nombre de la subcategoría *') }}
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('detail','Detalle *') }}
    {{ Form::textarea('detail',null,['class' => 'form-control', 'maxlength' => '200']) }}
    <p class="text-danger">{{ $errors->first('detail')}}</p>
</div>
<div class="form-group">
    {{ Form::label('category_id','Categoría') }}
    {{ Form::select('category_id',$categories,null,['class'=>'form-control','placeholder'=>'Seleccione categoría','required']) }}
</div>
<div class="form-group">
    {{ Form::label('megacategory_id','Megacategoría') }}
    {{ Form::select('megacategory_id',$megacategories,null,['class'=>'form-control','placeholder'=>'Seleccione megacategoría','required']) }}
</div>
<div class="form-group"> 
    {{ Form::label('channel','Canal *') }}
    {{ Form::text('channel',null,['class' => 'form-control', 'maxlength' => '200']) }}
    <p class="text-danger">{{ $errors->first('channel')}}</p>
</div>
<div class="form-group">
    {{ Form::submit('Guardar',['class' => 'btn btn-sm btn-outline-success']) }}
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