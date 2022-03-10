@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('SentimentWord.index') }}" method="get">
                        <div class="card-header">
                            <h4>Administración de palabras
                                @can('SentimentWord.create')
                                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#ModalCrear">Agregar nuevo palabra</button>
                                @endcan
                            </h4>
                        </div>

                        <div class="card-body table-responsive">
                            <div class="input-group">
                                <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                                <span class="input-group-prepend">
                                    <button type="submit" class="btn btn-outline-primary" id="seacrh">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </span>
                            </div> <br>

                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Palabra</th>
                                    <th>Sentimiento</th>
                                    <th>Acciones</th>
                                    <th colspan="3">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($word as $words)
                                    <tr>
                                        <td>{{ $words->id }}</td>
                                        <td>{{ $words->word}}</td>
                                        <td>{{ $words->sentiment}}</td>
                                        <td>
                                            <div class="list-group-item-figure">
                                                <button type="button" onclick="show({{ $words->id }})" data-user="{{ $words->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                                <a onclick="confirmation(event)" href="./SentimentWord/delete/{{$words->id}} " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                    <i class="icon-close"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row justify-content-center">
                            {{ $word->appends($_GET)->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL CREAR ---------------------------------------------------}}

    <div class="modal fade" id="ModalCrear"  tabindex="-1" role="dialog" aria-labelledby="ModalCrear" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva palabra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="word">Palabra</label>
                            <input type="text" class="form-control" id="word" placeholder="Ingrese un la palabra...">
                        </div>

                        <div class="form-group">
                            {{ Form::label('sentiment','Seleccione el sentimiento ') }}
                            {{ Form::select('sentiment', ['Positivo' => 'Positivo', 'Negativo' => 'Negativo'], null, ['placeholder' => 'Seleccione el sentimiento...','class' => 'form-control'])}}
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    {{--<button type="button" class="btn btn-primary" onclick="ModalCrear()">Guardar palabra</button>--}}
                    <button type="button" id="btn_guardar" class="btn btn-primary">Editar palabra</button>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL EDITAR --------------------------------------------------}}

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar información de la aplicación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="word_edit">Nombre</label>
                                <input type="text" class="form-control" id="word_edit" placeholder="Ingrese un nombre de la aplicación...">
                            </div>

                            <div class="form-group">
                                {{ Form::label('sentiment_edit','Seleccione el sentimiento ') }}
                                {{ Form::select('sentiment_edit', ['Positivo' => 'Positivo', 'Negativo' => 'Negativo'], null, ['placeholder' => 'Seleccione el sentimiento...','class' => 'form-control'])}}
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_edit_word" class="btn btn-primary">Editar palabra</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        let id_w=0;
        document.getElementById('btn_edit_word').addEventListener('click',ModalEditar);
        document.getElementById('btn_guardar').addEventListener('click',ModalCrear);

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        function ModalCrear() {
            $("#ModalCrear").modal('toggle');
            let word=document.getElementById('word').value,
                sentiment = document.getElementById('sentiment').value;
                data={word, sentiment};
            axios.post('{{ route('SentimentWord.store') }}', data).then(response => {
                window.location = "{{ route('SentimentWord.index') }}";
            }).catch(error=>{
                swal('Ops', 'No es posible crear una nueva palabra','warning');
            });

        }

        function show(id){
            let id_word = id ,
                data={id_word};
            id_w = id_word;
            axios.post("{{route('SentimentWord.edit')}}",data).then( response => {
                document.getElementById('word_edit').value = response.data[0].word;
                document.getElementById('sentiment_edit').value = response.data[0].sentiment;
            });
        }

        function ModalEditar() {
            $("#showModal").modal('toggle');
            let id_word = id_w,
                word_edit=document.getElementById('word_edit').value,
                sentiment_edit=document.getElementById('sentiment_edit').value,
                data={id_word,word_edit, sentiment_edit};

            axios.post('{{ route('SentimentWord.update') }}', data).then(response => {
                window.location = "{{ route('SentimentWord.index') }}";
            }).catch(error=>{
                swal('Ops', 'No es posible editar la palabra','warning');
            });
        }

        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        swal("Exito! Se ha eliminado de forma exitosa!", {
                            icon: "success",
                        });
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha eliminado!", "info");
                    }
                });
        }
    </script>
@endsection
