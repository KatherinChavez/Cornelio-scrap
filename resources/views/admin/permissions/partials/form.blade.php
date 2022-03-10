<div class="form-group">
    {{ Form::label('name','Nombre del permiso *') }}
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('description','Descripción del permiso *') }}
    {{ Form::text('description',null,['class' => 'form-control', 'maxlength' => '200', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('description')}}</p>
</div>
<div class="form-group">
    {{ Form::label('slug','Slug del permiso *') }}
    {{ Form::text('slug',null,['class' => 'form-control', 'maxlength' => '100', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('slug')}}</p>
</div>
<div class="form-group">
    {{ Form::submit('Guardar',['class' => 'btn btn-sm btn-outline-success', 'onclick'=>"this.disabled=true;this.value='Guardar datos... .';this.form.submit();"]) }}
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