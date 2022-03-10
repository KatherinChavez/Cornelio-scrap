@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4>Análisis de páginas </h4>
                    </div>
                    <div class="card-body table-responsive">
                        <form action="{{ route('analysis.index') }}" method="get">
                            <div class="form-group">
                                {{ Form::label('page_id','Seleccione la página  *') }}
                                {{ Form::select('page_id',$page,null,['class'=>'form-control col-12','placeholder'=>'Seleccione la página','required']) }}
                                <p class="text-danger">{{ $errors->first('description')}}</p>
                            </div>
                            <span class="input-group-prepend">
                                <button type="submit" class="btn btn-sm btn-block btn-primary btn-round" id="">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </span>

                             @if($data != 0)
                                {{--SE MUESTRA LAS ULTIMAS PUBLICACIONES DE LA PAGINA --}}
                                <div class="card-body">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold">
                                        Últimas publicaciones
                                    </h5>
                                    <div class="card table-responsive" id="content">

                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th width="10px"> Página </th>
                                                <th width="10px"> Contenido </th>
                                                <th width="10px"> Fecha </th>
                                            </thead>
                                            <tbody>
                                                @foreach ($post as $posts)
                                                    <tr>
                                                        <td>{{ $posts->page_name }}</td>
                                                        <td>{{ $posts->content }}</td>
                                                        <td>{{ $posts->created_at }}</td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="paginacion" class="row justify-content-center">
                                        {{ $post->appends($_GET)->links() }}
                                    </div>
                                </div>

                                {{--SE MUESTRA LAS PAGINAS QUE SE HAN SIDO CLASIFICADA --}}
                                <div class="card-body">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold">
                                        Últimas clasificaciones
                                    </h5>
                                    <div class="card table-responsive" id="content">

                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th width="10px"> Página </th>
                                                {{--<th width="10px"> Contenido </th>--}}
                                                <th width="10px"> Tema  </th>
                                                <th width="10px"> Fecha  </th>
                                            </thead>
                                            <tbody>
                                            @foreach ($clasification as $clasifications)
                                                <tr>
                                                    <td>{{ $clasifications->post->page_name }}</td>
                                                    {{--<td>{{ $clasifications->megacategory->name }}</td>--}}
                                                    <td>{{ $clasifications->subcategory->name }}</td>
                                                    <td>{{ $clasifications->created_at }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                    <div id="paginacion" class="row justify-content-center">
                                        {{ $clasification->appends($_GET)->links() }}
                                    </div>
                                </div>

                                {{--SE MUESTRA EL ULTIMO SCRAP DE REACCIONES --}}
                                <div class="card-body">
                                    <h5 class="mt-3 b-b1 pb-2 mb-4 fw-bold">
                                        Últimas reacciones
                                    </h5>
                                    <div class="card table-responsive" id="content">

                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th width="10px"> Página </th>
                                                <th width="10px"> <img src="{{ asset('reacciones/like.png') }}" alt="Like" title="Like" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="like" src="{{ asset('reacciones/love.png') }}"  alt="Love" title="Love" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="Hahaha" src="{{ asset('reacciones/hahaha.png') }}" alt="Hahaha" title="Hahaha" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="Wow" src="{{ asset('reacciones/wow.png') }}" alt="Wow" title="Wow" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="Sad" src="{{ asset('reacciones/sad.png') }}" alt="Sad" title="Sad" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="Angry" src="{{ asset('reacciones/angry.png') }}" alt="Angry" title="Angry" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> <img name="comentarios" src="{{ asset('reacciones/shared.png') }}" alt="comentarios" title="Comentarios" style="width: 18px; vertical-align: middle"> </th>
                                                <th width="10px"> Fecha </th>
                                            </thead>
                                            <tbody>
                                            @foreach ($reaction as $reactions)
                                                <tr>
                                                    <td>{{ $reactions->scrap->page_name }}</td>
                                                    <td>{{ $reactions->likes }}</td>
                                                    <td>{{ $reactions->love }}</td>
                                                    <td>{{ $reactions->haha }}</td>
                                                    <td>{{ $reactions->sad }}</td>
                                                    <td>{{ $reactions->wow }}</td>
                                                    <td>{{ $reactions->angry }}</td>
                                                    <td>{{ $reactions->shared }}</td>
                                                    <td>{{ $reactions->created_at }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="paginacion" class="row justify-content-center">
                                        {{ $reaction->appends($_GET)->links() }}
                                    </div>
                                </div>
                            @endif
                        <form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection