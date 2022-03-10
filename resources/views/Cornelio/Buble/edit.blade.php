@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4>Reporte de burbuja temática</h4></div>
                    <div class="card-body">
                        {!! Form::model($num, ['route' => ['bubles.update',$num->id],
                            'method' => 'PUT']) !!}

                            <div class="card-body table-responsive">
                                <div class="form-group">
                                    <input type="hidden" id="num" value="{{$num->id}}">
                                    <div class="form-group">
                                        <p><strong>Nombre de la empresa: </strong>{{ $company_name->nombre }}</p>
                                    </div>

                                    <div class="form-group">
                                        <p><strong>Nombre / descripción: </strong>{{ $num->descripcion }}</p>
                                    </div>

                                    {{-----------------------------------------------------------------------------------}}
                                    @if($num->group_id != null)
                                        <div class="form-group">
                                            <p><strong>Indetificador de grupo: </strong>{{ $num->group_id }} </p>
                                        </div>
                                    @elseif($num->numeroTelefono != null)
                                        <div class="form-group">
                                            <p><strong>Número de teléfono: </strong>{{ $num->numeroTelefono }} </p>
                                        </div>
                                    @endif

                                    {{-----------------------------------------------------------------------------------}}
                                    <div class="form-group">
                                        {{ Form::label('nombre','Tipo de reporte ') }}
                                        @if($num->report == 13)
                                            <p><strong>Reporte matutino</strong></p>
                                        @elseif($num->report == 14)
                                            <p><strong>Reporte al medio día</strong></p>
                                        @elseif($num->report == 15)
                                            <p><strong>Reporte al finalizar la tarde</strong></p>
                                        @elseif($num->report == 16)
                                            <p><strong>Reporte al finalizar el día</strong></p>
                                        @endif
                                    </div>

                                    @if(isset($getContent))
                                        <div class="form-group" id="content">
                                            {{ Form::label('nombre','Contenidos seleccionados ') }}
                                            <ul style="list-style-type:circle">
                                                @foreach ($getContent as $get)
                                                    <li><label>{{ $get->name }}</label></li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        {{------------------------------------------------------------------------------------}}
                                        <div class="form-group" id="optionDelete">
                                            <h6><b>
                                                Si deseas eliminar algunos de los contenidos que se encuentran actualmente
                                                para recibir el reporte de burbuja, selecciona el siguiente botón para
                                                desplegar la opción de eliminar el contenido que desees
                                            </b></h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger" style="width: 35%" onclick="destroyContent()">Eliminar contenido</button>
                                        </div>

                                        <div class="form-group" id="contentDelete" >
                                            {{ Form::label('nombre','Seleccione el contenido que deseas eliminar') }}
                                            <ul style="list-style-type:circle">
                                                @foreach ($getContent as $get)
                                                    <li>
                                                        <label>
                                                            {{ Form::checkbox('deleteContents[]', $get->id, null)}}
                                                            {{ $get->name }}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif


                                    {{------------------------------------------------------------------------------------}}

                                    <hr>
                                    <div class="form-group">
                                        @if(count($contents) > 0)
                                            <h3>Lista de los contenidos </h3>
                                            <p><b>Seleccione los contenidos que deseas recibir en los reportes</b></p>
                                            <ul class="list-unstyled">
                                                @foreach ($contents as $content)
                                                    <li>
                                                        <label>
                                                            {{ Form::checkbox('contents[]', $content->id, null)}}
                                                            {{ $content->name }}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <h3>No cuenta con contenido para seleccionar </h3>
                                        @endif

                                    </div>

                                    {{------------------------------------------------------------------------------------}}
                                    <div class="form-group">
                                        <input class="btn btn-sm btn-success pull-left m-1" style="width: 20%" onclick="this.disabled=true;this.value='Guardar datos....';this.form.submit();" name="commit" value="Guardar " type="submit">
                                        <a href="{{ route('bubles.index') }}"  class="btn btn-sm btn-danger pull-left m-1" style="width: 20%" >Atras</a>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        if(document.getElementById("contentDelete") != null){
            document.getElementById("contentDelete").hidden = true;
        }

        function destroyContent() {
            document.getElementById("contentDelete").hidden = false;
            document.getElementById("optionDelete").hidden  = true;
            document.getElementById("content").hidden       = true;
        }
    </script>
@endsection
