@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4>Search user in comments</h4>
                </div>
                <div class="card-body table-responsive">
                    <div class="input-group">
                        <input type="search" name="users" id="users" class="form-control border-info" placeholder="Buscar">
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
        document.getElementById('nav-user').className+=' active';

        function search() {
            let CommentList = '',
                datos = {},
                users = document.getElementById('users').value;
            datos = {users};
            axios.post('{{ route('Search.searchUser',$company) }}', datos).then(response => {
                CommentList += `<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10px">Page</th>
                                <th>Content post</th>
                                <th>Name</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>`;
                let comentario = response.data;

                comentario.forEach(comments => {
                    CommentList += `<tr>
                        <td>`+ comments.page_name+`</td>
                        <td>`+ comments.content+`</td>
                        <td>`+ comments.commented_from+`</td>
                        <td>`+ comments.comment+`</td>
                        <td>`+ comments.created_time+`</td>
                    </tr>
                    `;
                });
                CommentList += `</tbody>
                            </table>`;
                document.getElementById('content').innerHTML = CommentList;
            });
        }
    </script>
@endsection
