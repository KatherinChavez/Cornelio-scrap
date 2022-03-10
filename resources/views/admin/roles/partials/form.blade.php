<div class="form-group">
    {{ Form::label('name','Nombre del Usuario *') }}
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('slug','URL amigable *') }}
    {{ Form::text('slug',null,['class' => 'form-control', 'maxlength' => '150', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('slug')}}</p>
</div>
<div class="form-group">
    {{ Form::label('description','Descripción *') }}
    {{ Form::textarea('description',null,['class' => 'form-control', 'maxlength' => '200', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('description')}}</p>
</div>
{{--<hr>--}}
{{--<h3>Permiso especial</h3>--}}
{{--<div class="form-group">--}}
    {{--<label>{{ Form::radio('special','all-access') }} Acceso total </label>--}}
    {{--<label>{{ Form::radio('special','no-access') }} Ningún acceso</label>--}}
{{--</div>--}}
<hr>
<h3>Lista de permisos</h3>
<div class="form-group">
    <ul class="list-unstyled">
        @foreach ($permissions as $permission)
            <li>
                <label>
                    {{ Form::checkbox('permissions[]', $permission->id, null)}}
                    {{ $permission->name }}
                    <em>({{ $permission->description ? : 'N/A'}})</em>
                </label>
            </li>
        @endforeach
    </ul>
</div>
<div class="form-group">
    {{--{{ Form::submit('Guardar',['class' => 'btn btn-sm btn-outline-success']) }}--}}
    <input class="btn btn-sm btn-success pull-left" onclick="this.disabled=true;this.value='Guardar datos... .';this.form.submit();" name="commit" value="Guardar " type="submit">

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
