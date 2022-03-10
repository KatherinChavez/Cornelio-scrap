<div class="form-group">
    {{ Form::label('palabra','Palabra *') }}
    {{ Form::text('palabra',null,['class' => 'form-control', 'maxlength' => '80', 'required' => 'required']) }}
    <p class="text-danger">{{ $errors->first('palabra')}}</p>
</div>
<div class="form-group">
    {{ Form::label('detalle','Detalle *') }}
    {{ Form::textarea('detalle',null,['class' => 'form-control', 'maxlength' => '200', 'required' => 'required']) }}
    <p class="text-danger">{{ $errors->first('detalle')}}</p>
</div>
<div class="form-group">
    {{ Form::label('subcategoria_id','Tema *') }}
    {{ Form::select('subcategoria_id',$subcategories,null,['class'=>'form-control','placeholder'=>'Seleccione un tema','required' => 'required']) }}
    <p class="text-danger">{{ $errors->first('subcategoria_id')}}</p>
</div>
<div class="form-group">
    {{ Form::label('prioridad','Tipo') }}
    {{ Form::select('prioridad', ['1' => 'Alta', '2' => 'Medio', '3' => 'Bajo'], null, ['placeholder' => 'Seleccione...','class' => 'form-control','required' => 'required']) }}
    <p class="text-danger">{{ $errors->first('prioridad')}}</p>
</div>


<div class="form-group">
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
