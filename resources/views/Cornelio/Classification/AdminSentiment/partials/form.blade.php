<div class="form-group">
    {{ Form::label('sentiment','Nombre del sentimiento *') }}
    {{ Form::text('sentiment',null,['class' => 'form-control', 'maxlength' => '80']) }}
    <p class="text-danger">{{ $errors->first('sentiment')}}</p>
</div>
<div class="form-group">
    {{ Form::label('sentiment_detail','Detalle del sentimiento*') }}
    {{ Form::textarea('sentiment_detail',null,['class' => 'form-control', 'maxlength' => '200']) }}
    <p class="text-danger">{{ $errors->first('sentiment_detail')}}</p>
</div>
<div class="form-group">
    {{ Form::label('page_name','Página') }}
    {{ Form::select('page_name',$pages,null,['class'=>'form-control','placeholder'=>'Seleccione la página','required']) }}
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
