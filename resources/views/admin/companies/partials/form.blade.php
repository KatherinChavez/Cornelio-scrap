<div class="form-group">
    {{ Form::label('nombre','Nombre de la empresa *') }}
    {{ Form::text('nombre',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('nombre')}}</p>
</div>
<div class="form-group">
    {{ Form::label('slug','URL amigable *') }}
    {{ Form::text('slug',null,['class' => 'form-control', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('slug')}}</p>
</div>

<div class="form-group col-sm-12 my-1 form-group">
    {{ Form::label('page','Seleccione la página principal*') }}
    {{ Form::select('page',$page,null,['class'=>'form-control','placeholder'=>'Seleccione una opción','required']) }}
    <p class="text-danger">{{ $errors->first('description')}}</p>
</div>

<div class="form-group">
    {{ Form::label('channel','ID del canal de Telegram  *') }}
    <p>Ingresa a tu canal de telegram el bot "CornelioMonitoreo" como administrador para recibir notificaciones
        y de tal forma administrar las clasificaciones</p>
    {{ Form::text('channel',null,['class' => 'form-control', 'maxlength' => '15', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('channel')}}</p>
</div>

<div class="form-group">
    {{ Form::label('client_id','Cliente id de WAPIAD  *') }}
    {{ Form::text('client_id',null,['class' => 'form-control', 'placeholder'=>'', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('client_id')}}</p>
</div>

<div class="form-group">
    {{ Form::label('instance','Instancia de WAPIAD  *') }}
    {{ Form::text('instance',null,['class' => 'form-control', 'placeholder'=>'', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('instance')}}</p>
</div>

<div class="form-group">
    {{ Form::label('group_id','Id del grupo  *') }}
    {{ Form::text('group_id',null,['class' => 'form-control', 'placeholder'=>'', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('group_id')}}</p>
</div>

<div class="form-group">
    {{ Form::label('phone','Número de teléfono  *') }}
    {{ Form::text('phone',null,['class' => 'form-control', 'maxlength' => '15','minlength' => '9','placeholder'=>'50667542314', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('phone')}}</p>

    {{ Form::text('phoneOptional',null,['class' => 'form-control', 'maxlength' => '15','minlength' => '9','placeholder'=>'50667542314',  'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('phoneOptional')}}</p>
</div>

<div class="form-group">
    {{ Form::label('emailCompanies','Correo de la empresa *') }}
    {{ Form::text('emailCompanies',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('emailCompanies')}}</p>
</div>

<div class="form-group">
    {{ Form::label('key','Clave del número de teléfono *') }}
    {{ Form::text('key',null,['class' => 'form-control', 'maxlength' => '200', ]) }}
    <p class="text-danger">{{ $errors->first('key')}}</p>
</div>

<div class="form-group">
    {{ Form::label('descripcion','Descripción de la empresa *') }}
    {{ Form::textarea('descripcion',null,['class' => 'form-control', 'maxlength' => '200', ]) }}
    <p class="text-danger">{{ $errors->first('descripcion')}}</p>
</div>

<div class="form-group">
    {{ Form::label('status','Estado *') }}
    <br>{{ Form::radio('status','1') }} Activo
    {{ Form::radio('status','0') }} Inactivo
    <p class="text-danger">{{ $errors->first('status')}}</p>
</div>

<hr>
<h3>Lista de Usuarios</h3>
<div class="form-group">
    <ul class="list-unstyled">
        @foreach ($user as $users)
            <li>
                <label>
                    {{ Form::checkbox('users[]', $users->id, null)}}
                    {{ $users->name }}
                    {{--<em>({{ $role->description ? : 'N/A'}})</em>--}}
                </label>
            </li>
        @endforeach
    </ul>
</div>


<div class="form-group">
    {{--{{ Form::submit('Guardar',['class' => 'btn btn-sm btn-success']) }}--}}
    <input class="btn btn-sm btn-success pull-left" onclick="this.disabled=true;this.value='Guardar datos... .';this.form.submit();" name="commit" value="Guardar " type="submit">

</div>

@section('script')
    <script>
        $(document).ready(function(){
            $("#nombre").on('keypress', function(e) {
                let regex = new RegExp("^[a-zA-Z ]*$");
                let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });

            $("#chanel").on('keypress', function(e) {
                let regex = new RegExp("^[-0-9 ]*$");
                let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        });

        $(document).ready(function(){
            $("#chanel").on('keypress', function(e) {
                let regex = new RegExp("^[-0-9 ]*$");
                let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        });

        $(document).ready(function(){
            $("#chanel").on('keypress', function(e) {
                let regex = new RegExp("^[-0-9 ]*$");
                let str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        });
    </script>
@endsection
