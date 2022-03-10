@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>Last 100 </h4></div>
                <input id="user"type="hidden" value="{{ Auth::user()->id }}">
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <label for="paginas">Seleccione la pagina</label>
                        <select id="paginas" name="pagina" class='form-control'></select>
                        <p class="text-danger">{{ $errors->first('description')}}</p>
                    </div>
                    <div class="form-group">
                        <button type="button" id="seleccionar" class="btn btn-sm btn-outline-primary"onclick="setData()">Seleccionar página</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                isLogedIn();

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }
        function isLogedIn() {
            var pageAccessToken='';
            cargarData();
        }
        function cargarData() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var user=document.getElementById('user').value;
            $.ajax({
                method: "POST",
                url: "<?php echo route('scrapsLast.page'); ?>",
                data: "_token=" + CSRF_TOKEN+
                "&user_id="+user,
                success: function (data) {
                    console.log(data);
                    misPaginas(data);
                }
            })

        }

        function misPaginas(data) {
            var consulta = data;
            var categoryLists = '<option selected value="0">Seleccione una Paginas...</option>';
            for (var index in consulta) {
                var id=consulta[index].page_id;
                var name=consulta[index].page_name;
                categoryLists += '<option value='+id+'>'+name+'</option>';
                $('#paginas').html(categoryLists);
            };
        }

        function setData() {
            showProcessing();
            let datos = {};
                pagina=document.getElementById('paginas').value;
                pagina=document.getElementById('paginas').value;
                name = $('#paginas option:selected').text();
            getInformation(pagina,name);
            FB.api(
                '/'+pagina+'',
                'GET',
                {"fields":"access_token"},
                function(response) {
                    if(response.access_token){
                        //console.log(response);
                        pageAccessToken=response.access_token;
                        if(pagina!=0){
                            datos = {pagina,name};
                            axios.post('{{ route('scrapsLast.lastPost') }}', datos).then(response => {
                                console.log(datos);
                                hideProcessing();
                                alert("Terminado...");
                                var newLocation="<?php echo route('scrapsLast.ScrapLast')?>";
                                window.location = newLocation;
                            });
                        }
                    }else{
                        FB.api(
                            '/me',
                            'GET',
                            {"fields":"accounts"},
                            function(response) {
                                //console.log(response);
                                pageAccessToken=response.accounts.data[0].access_token;
                                if(pagina!=0){
                                    axios.post('{{ route('scrapsLast.lastPost',$company) }}', datos).then(response => {
                                    console.log(datos);
                                    hideProcessing();
                                    alert("Terminado...");
                                    var newLocation="<?php echo route('scrapsLast.ScrapLast',$company)?>";
                                    window.location = newLocation;
                                    });
                                }
                            }
                        );
                    }

                }
            );

        }

        function getInformation(page_id,nombre){
            FB.api(
                "/"+page_id,
                'GET',
                {"fields":"fan_count,category,about,company_overview,location,phone,emails,talking_about_count"},
                function(response) {
                    saveInformation(response,page_id,nombre);
                }
            );
        }

        function saveInformation(page,page_id,nombre){
            let datos = {},
            page_name=nombre;
            fan_count=page.fan_count;
            category =page.category;
            about =page.about;
            company_overview =page.company_overview;
            phone=page.phone;
            email=page.emails;
            talking=page.talking_about_count;
            
            datos = {page_id, page_name, fan_count, category, about , company_overview, phone,email, talking},
            axios.post('{{ route('scrapsAll.infoPage',$company) }}', datos).then(response => {
                
            });
        }
        function showProcessing() {
            $("body").append(
                '<div id="overlay-processing" style="background: #F0F0F0; height: 100%; width: 100%; opacity: .7; padding-top: 10%; position: fixed; text-align: center; top: 0;z-index: 2147483647;"><h2 style="color: #333333">Processing...</h2></div>'
            )
        }

        function hideProcessing() {
            $('body').find('#overlay-processing').remove();
        }    
    </script>
@endsection