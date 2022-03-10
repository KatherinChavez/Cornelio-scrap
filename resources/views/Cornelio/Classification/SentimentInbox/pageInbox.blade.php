@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        Conversaciones
                        <a onclick="" class="mx-2" data-toggle="popover" title="Obtener información" data-content="Seleccione la página que desea obtener las conversaciones de Facebook">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h4>
                </div>
                <div class="card-body table-responsive">
                    <input id="user"type="hidden" value="{{ Auth::user()->id }}">

                    <label for="paginas">Seleccione la página</label>
                    <select class="lista-pages lista form-control" id="pagina" name="pagina">
                        <option selected value="">Seleccione una Página...</option>
                    </select>
                    <br>
                    <div class="form-group">
                        <button class="btn btn-sm btn-block btn-primary btn-round" type="button" value="scrap Inbox" onclick="getDatos()">Obtener conversación</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        // document.getElementById('nav-individual').className+=' active';

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn();

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }

        function isLogedIn() {
            FB.api(
                '/me',
                'GET',
                {"fields":"accounts"},
                function(response) {
                    if(response.error){
                        swal("Sin datos!", "Lamentamos informarle que en estos momentos no se puede mostrar las páginas!", "warning");
                    }else{
                        generatePageLists(response);
                    }
                }
            );
        }

        function generatePageLists(response) {
            var pageLists = '';
            var data=response.accounts.data;
            data.forEach( function(element, index) {
                pageLists += '<option value="'+element.id+'">'+element.name+'</option>';

            });
            $('.lista-pages').append(pageLists);
            if(response.accounts.paging.next){
                var siguiente=response.accounts.paging.next;
                next(siguiente);
            }

        }

        function generatePageLists2(response) {
            var pageLists = '';
            var data=response.data;
            data.forEach( function(element, index) {
                pageLists += '<option value="'+element.id+'">'+element.name+'</option>';
                // alert(tokenUser);

            });
            $('.lista-pages').append(pageLists);
            if(response.paging.next){
                var siguiente=response.paging.next;
                next(siguiente);
            }

        }

        function next(next) {
            FB.api(
                next,
                function (response) {
                    generatePageLists2(response);

                }
            )
        }

        function getDatos() {
            var page=document.getElementById('pagina').value;
            if(page===''){
                swal('Opss','por favor seleccione una página','warning');
                return false;
            }

            FB.api(
                '/'+page+'',
                'GET',
                {"fields":"access_token,name"},
                function(response) {
                   scrap(response);
                }
            );

        }

        function scrap(response) {
            let datos = {},
                page=response.id,
                page_name=response.name,
                access_token=response.access_token,
                user=document.getElementById('user').value;
            window.location="{{ route('SentimentInbox.selectInbox') }}?page="+page+" ";

        }


    </script>
@endsection
