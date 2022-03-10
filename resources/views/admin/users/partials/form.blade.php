
{{--<div class="form-group">--}}
    {{--<label for="name" class="placeholder"><b>{{ __('Nombre *') }}</b></label>--}}
    {{--{{ Form::text('name',old('name'),['class' => 'form-control', 'maxlength' => '80', 'required']) }}--}}
      {{--@if ($errors->has('name'))--}}
        {{--<span class="invalid-feedback" role="alert">--}}
                        {{--<strong>{{ $errors->first('name') }}</strong>--}}
                    {{--</span>--}}
    {{--@endif--}}
{{--</div>--}}
{{--<div class="form-group">--}}
    {{--<label for="last_name" class="placeholder"><b>{{ __('Primer apellido *') }}</b></label>--}}
    {{--{{ Form::text('last_name',old('last_name'),['class' => 'form-control', 'maxlength' => '80', 'required','autofocus', 'tabindex'=>'3']) }}--}}
    {{--@if ($errors->has('last_name'))--}}
        {{--<span class="invalid-feedback" role="alert">--}}
                        {{--<strong>{{ $errors->first('last_name') }}</strong>--}}
                    {{--</span>--}}
    {{--@endif--}}
{{--</div>--}}
{{--<div class="form-group">--}}
    {{--<label for="sec_last_name" class="placeholder"><b>{{ __('Segundo apellido') }}</b></label>--}}
    {{--{{ Form::text('sec_last_name',old('sec_last_name'),['class' => 'form-control', 'maxlength' => '80', 'required','autofocus', 'tabindex'=>'4']) }}--}}
   {{--@if ($errors->has('sec_last_name'))--}}
        {{--<span class="invalid-feedback" role="alert">--}}
                        {{--<strong>{{ $errors->first('sec_last_name') }}</strong>--}}
                    {{--</span>--}}
    {{--@endif--}}
{{--</div>--}}
{{--<div class="form-group">--}}
{{--    <label for="phone" class="placeholder"><b>{{ __('Telefono Movil *') }}</b></label>--}}
{{--    <input id="phone" type="text" maxlength="11" placeholder="87654321" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" required tabindex=5>--}}
{{--    @if ($errors->has('phone'))--}}
{{--        <span class="invalid-feedback" role="alert">--}}
{{--                        <strong>{{ $errors->first('phone') }}</strong>--}}
{{--                    </span>--}}
{{--    @endif--}}
{{--</div>--}}
{{--<div class="form-group">--}}
{{--    <label for="telegram" class="placeholder"><b>{{ __('Telegram Username') }}</b></label>--}}
{{--    <input id="telegram" type="text" maxlength="20" placeholder="username" class="form-control{{ $errors->has('telegram') ? ' is-invalid' : '' }}" name="telegram" value="{{ old('telegram') }}" tabindex=6>--}}
{{--    @if ($errors->has('telegram'))--}}
{{--        <span class="invalid-feedback" role="alert">--}}
{{--                        <strong>{{ $errors->first('telegram') }}</strong>--}}
{{--                    </span>--}}
{{--    @endif--}}
{{--</div>--}}


<div class="form-group">
    {{ Form::label('name','Nombre del usuario *') }}
    {{ Form::text('name',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('last_name','Primer apellido *') }}
    {{ Form::text('last_name',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('last_name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('sec_last_name','Segunda apellido *') }}
    {{ Form::text('sec_last_name',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('sec_last_name')}}</p>
</div>
<div class="form-group">
    {{ Form::label('email','Correo electrónico *') }}
    {{ Form::text('email',null,['class' => 'form-control', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('email')}}</p>
</div>



{{--<div class="form-group">--}}
    {{--<label for="email" class="placeholder"><b>{{ __('Correo electrónico *') }}</b></label>--}}
    {{--{{ Form::text('email',null,['class' => 'form-control', 'maxlength' => '80', 'required'=>'required']) }}--}}

{{--</div>--}}
<hr>
<h3>Lista de Roles</h3>
<div class="form-group">
    <ul class="list-unstyled">
        @foreach ($roles as $role)
            <li>
                <label>
                    {{ Form::checkbox('roles[]', $role->id, null)}}
                    {{ $role->name }}
                    <em>({{ $role->description ? : 'N/A'}})</em>
                </label>
            </li>
        @endforeach
    </ul>
</div>

<h3>Lista de empresas</h3>
<div class="form-group">
    <ul class="list-unstyled" id="companyList">
        @foreach ($companies as $company)
            <li>
                <label>
                    {{ Form::checkbox('companies[]', $company->id, null)}}
                    {{ $company->nombre }}
                    <em>({{ $company->descripcion ? : 'N/A'}})</em>
                </label>
            </li>
        @endforeach
        <p class="text-danger">{{ $errors->first('companies')}}</p>
    </ul>
</div>
<div class="form-group">
    {{--{{ Form::submit('Guardar',['class' => 'btn btn-sm btn-outline-success']) }}--}}
    <input class="btn btn-sm btn-success pull-left" onclick="this.disabled=true;this.value='Guardar datos... .';this.form.submit();" name="commit" value="Guardar " type="submit">

</div>
@section('script')
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
@endsection
