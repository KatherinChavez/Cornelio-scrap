<div class="form-group">
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('description','Descripción *') }}
    {{ Form::textarea('description',null,['class' => 'form-control', 'maxlength' => '200']) }}
    <p class="text-danger">{{ $errors->first('description')}}</p>
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