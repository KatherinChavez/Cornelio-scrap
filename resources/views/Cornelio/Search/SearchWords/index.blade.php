@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Search words in posts</h4>
                </div>
                <div class="card-body table-responsive">
                    <div class="input-group">
                        <input type="search" name="palabra" id="palabra" class="form-control border-info" placeholder="Buscar">
                        <span class="input-group-prepend">
                            <button type="submit" class="btn btn-outline-primary" onclick="search()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </span>
                    </div>
                    <div class="card table-responsive" id="content"></div>
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

            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index',$company) }}";
            } else {
                window.location = "{{ route('facebook.index',$company) }}";
            }
        }
        function search() {
            let PostsList = '',
                datos = {},
                palabra = document.getElementById('palabra').value;
                datos = {palabra};
            axios.post('{{ route('Search.searchWords',$company) }}', datos).then(response => {

                PostsList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10px">Page</th>
                                <th>Content post</th>
                                <th>Date</th>
                                <th>Facebook view</th>
                                <th>View on app</th>
                                <th colspan="5">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>`;
                let publicacion = response.data;
                publicacion.forEach(posts => {
                    let posteoid=posts.post_id,
                        result=posteoid.lastIndexOf("_"),
                        otr='';
                    if(result>0){
                        otr=posteoid.slice(result+1);
                    }
                    PostsList += `<tr>
                        <td>`+ posts.page_name+`</td>
                        <td>`+ posts.content+`</td>
                        <td>`+ posts.created_time+`</td>
                        <td>
                            <a href="https://www.facebook.com/`+posts.page_id+`/posts/`+otr+`" target="_blank" class="btn btn-sm btn-outline-dark">
                                Facebook view
                            </a>
                        </td>
                        <td>
                            <a href="https://agenciadigitalcostarica.com/fscrdata/facebook/" target="_blank" class="btn btn-sm btn-outline-dark">
                            View on app
                            </a>
                        </td>
                    </tr>
                    `;
                });
                PostsList += `</tbody>
                </table>`;
                document.getElementById('content').innerHTML = PostsList;
            });
        }
    </script>
@endsection
