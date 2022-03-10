<div class="form-group">
    {{ Form::label('numeroTelefono','Número de Whatsapp *') }}
    {{ Form::text('numeroTelefono',null,['class' => 'form-control', 'maxlength' => '15','required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('numeroTelefono')}}</p>
</div>
<div class="form-group">
    {{ Form::label('descripcion','Descripción *') }}
    {{ Form::textarea('descripcion',null,['class' => 'form-control', 'maxlength' => '200', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('descripcion')}}</p>
</div>
<div class="form-group">
    {{ Form::label('subcategory_id','Tema *') }}
    {{ Form::select('subcategory_id',$subcategories,null,['class'=>'form-control', 'placeholder'=>'Seleccione un tema', 'required'=>'required']) }}
    <p class="text-danger">{{ $errors->first('subcategory_id')}}</p>
</div>
<div class="form-group">
    {{--{{ Form::submit('Guardar',['class' => 'btn btn-sm btn-success']) }}--}}
    <input class="btn btn-sm btn-success pull-left"
           onclick="this.disabled=true;this.value='Guardar datos... .';this.form.submit();" name="commit"
           value="Guardar " type="submit">
</div>


@section('script')
    <script>
        $(document).ready(function () {
            $("#numeroTelefono").mask("00000000000");
        })
    </script>
@endsection
