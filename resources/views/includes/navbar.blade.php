<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="white">
        <a href="{{ url('/home') }}" class="logo mr-2">

            <img src="{{ asset('img/logo.png')}}" alt="{{ config('app.name') }}" class="navbar-brand w-75">
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="icon-menu"></i>
            </span>
        </button>
        <button class="topbar-toggler more"><i class="fas fa-caret-square-down"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <!-- End Logo Header -->
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="white">
        <div class="container-fluid">
            <div class="ml-3">
                <div class="row">
                    <div><h3 class="fw-light"> ¡Hola de nuevo, {{Auth::user()->name}}!</h3></div>
                    <span><i class="far fa-grin-stars fa-2x ml-3"></i></span>
                </div>
            </div>
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                @yield('lang')

                {{--<li class="nav-item dropdown hidden-caret my-first-tour submenu" id="run" title="Tour informativo">--}}
                    {{--<a class="nav-link my-first-tour" href="#" aria-expanded="false" id="element13">--}}
                        {{--<i class="fas fa-question-circle"></i>--}}
                    {{--</a>--}}
                {{--</li>--}}

                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ asset('img/perfil.png') }}" alt="image profile"
                                 class="avatar-img rounded-circle my-first-tour" id="element10">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg"><img src="{{ asset('img/perfil.png') }}" alt="image profile"
                                                                class="avatar-img rounded"></div>
                                    <div class="u-text">
                                        <h4>{{ Auth::user()->name }}</h4>
                                        <p class="text-muted">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{ route('users.profile', Auth::user()->id) }}" >
                                    Perfil de usuario
                                </a>

                                {{--<a class="dropdown-item" href="{{ route('users.profile', Auth::user()->id)}}">--}}
                                    {{--Perfil de usuario--}}
                                {{--</a>--}}

                            </li>
                            <li class="nav-item" title="compañia activa: {{$company}}">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('companies') }}">
                                    Cambiar de empresa
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                   swal('Gracias por visitarnos.','Su sesión ha finalizado correctamente.');
                                   setTimeout(function(){ document.getElementById('logout-form').submit(); }, 2000);
                                    localStorage.clear();
                                    sessionStorage.clear()">
                                    {{ __('Cerrar sesión') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

<!-- Sidebar -->
@include('includes.sidebar')
<!-- End Sidebar -->
