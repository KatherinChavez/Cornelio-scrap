<div class="form-group">
    {{ Form::label('page_id','Id de la página') }}
    {{ Form::text('page_id',null,['class' => 'form-control', 'disabled']) }}
    <p class="text-danger">{{ $errors->first('page_id')}}</p>
</div>
<div class="form-group">
    {{ Form::label('page_name','Nombre de la página') }}
    {{ Form::text('page_name',null,['class' => 'form-control', 'disabled']) }}
    <p class="text-danger">{{ $errors->first('page_name')}}</p>
</div>
<hr style="width:100%;text-align:left;margin-left:0">
<div class="row justify-content-center">
    <div class="col-md-12 form-group">
        <label>Seleccione el tiempo y la aplicación para extraer las publicaciones de la página</label>
    </div>
    <div class="col-md-6 form-group">
        {{ Form::label('timePost','Periodo *') }}
        {{ Form::select('timePost', ['5' => 'Cada 5 minutos',
                                     '20' => 'Cada 20 minutos',
                                     '45' => 'Cada 45 minutos',
                                     '60' => 'Cada hora',
                                     '180' => 'Cada tres hora',
                                     '360' => 'Cada seis hora',
                                     '540' => 'Cada nueve hora',
                                     '1440' => 'Una vez al día',
                                     '720' => 'Dos veces al día',
                                     '4320' => 'Cada tres días',
                                     '10080' => 'Una vez a la semana',
                                     '21600' => 'Cada 15 días',
                                     '43800' => 'Una vez al mes'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}

        {{--{{ Form::select('timePost', ['5' => 'Cada 5 minutos', '20' => 'Cada 20 minutos', '45' => 'Cada 45 minutos', '60' => 'Una vez por hora', '1440' => 'Una vez al día', '720' => 'Dos veces al día'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}--}}
        <p class="text-danger">{{ $errors->first('time')}}</p>
    </div>
    <div class="col-md-6 form-group">
        {{ Form::label('id_appPost','Aplicación *') }}
        {{ Form::select('id_appPost',$app,null,['class'=>'form-control', 'placeholder'=>'Seleccione una aplicación', 'required'=>'required']) }}
        <p class="text-danger">{{ $errors->first('subcategory_id')}}</p>
    </div>
</div>

<hr style="width:100%;text-align:left;margin-left:0">
<div class="row justify-content-center">
    <div class="col-md-12 form-group">
        <label for="">Seleccione el tiempo y la aplicación para extraer las reacciones de la página</label>
    </div>
    <div class="col-md-6 form-group">
        {{ Form::label('timeReaction','Periodo *') }}
        {{ Form::select('timeReaction', ['5' => 'Cada 5 minutos',
                                         '20' => 'Cada 20 minutos',
                                         '45' => 'Cada 45 minutos',
                                         '60' => 'Cada hora',
                                         '180' => 'Cada tres hora',
                                         '360' => 'Cada seis hora',
                                         '540' => 'Cada nueve hora',
                                         '1440' => 'Una vez al día',
                                         '720' => 'Dos veces al día',
                                         '4320' => 'Cada tres días',
                                         '10080' => 'Una vez a la semana',
                                         '21600' => 'Cada 15 días',
                                         '43800' => 'Una vez al mes'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}

        {{--{{ Form::select('timeReaction', ['5' => 'Cada 5 minutos', '20' => 'Cada 20 minutos', '45' => 'Cada 45 minutos', '60' => 'Una vez por hora', '1440' => 'Una vez al día', '720' => 'Dos veces al día'], null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control'])}}--}}
        <p class="text-danger">{{ $errors->first('time')}}</p>
    </div>
    <div class="col-md-6 form-group">
        {{ Form::label('id_appReaction','Aplicación *') }}
        {{ Form::select('id_appReaction',$app,null,['class'=>'form-control', 'placeholder'=>'Seleccione una aplicación', 'required'=>'required']) }}
        <p class="text-danger">{{ $errors->first('subcategory_id')}}</p>
    </div>
</div>

<div class="form-group">
    {{ Form::label('limit_time','Tiempo limite para inactivar página *') }}
    {{ Form::select('limit_time', ['60' => 'Una hora',
                                     '180' => 'Tres horas',
                                     '480' => 'Ocho horas',
                                     '720' => 'Doce horas',
                                     '1440' => 'Un día',
                                     '4320' => 'Tres días',
                                     '7200' => 'Cinco días',
                                     '10080' => 'Siete días',
                                     '21600' => 'Quince días',
                                     '43200' => 'Treinta días'],
                                      null, ['placeholder' => 'Seleccione el tiempo...','class' => 'form-control', 'required'])}}
    <p class="text-danger">{{ $errors->first('limit_time')}}</p>
</div>

<div class="form-group">
    {{ Form::label('limit','Limite para extracción de publicación de Facebook *') }}
    {!! Form::number('limit', null, ['min' => '1' ,'max'=>100,'class' => 'form-control', 'id' => 'limit','required']) !!}
    <p class="text-danger">{{ $errors->first('limit')}}</p>
</div>

<div class="form-group">
    {{ Form::submit('Guardar',['class' => 'btn btn-sm btn-primary']) }}
</div>