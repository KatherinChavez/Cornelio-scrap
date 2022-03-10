<div class="sidebar sidebar-style-2" data-background-color="blue2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-color-light">
                @if(!session()->has('company'))
                    <li class="nav-item">
                        <a href="/home">
                            <i class="fas fa-home"></i>
                            <p>Inicio</p>
                        </a>
                    </li>
                @endif
                @if(session()->has('company'))
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img
                                src="https://avatars.dicebear.com/api/bottts/{{$side= (isset($company) ? $company : " ")}}.svg"
                                class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a>
								<span>
									{{$side= (isset($company) ? $company : " ")}}
									<span class="user-level">Empresa en gestión</span>
								</span>
                            </a>
                        </div>
                    </div>

                    <li id="nav-categorias" class="nav-item {{(Request::is('Tops','Tops/*'))?'active':''}}">
                        <a href="{{route('get.tops')}}">
                            <i class="fas fa-home"></i>
                            <p class="elements label label-default my-first-tour" id="element2">Inicio</p>
                        </a>
                    </li>

                    @can('users.all')
                        <li id="nav-categorias" class="nav-item {{(Request::is('index'))?'active':''}}" title="Mis paginas">
                            <a href="{{route('facebook.index')}}">
                                <i class="fas fa-swatchbook"></i>
                                <p class="elements label label-default my-first-tour" id="element2">Páginas</p>
                            </a>
                        </li>
                    @endcan



                    @can('users.all')
                        <li id="nav-categorias" class="nav-item {{(Request::is('topics','topics/*'))?'active':''}}">
                            <a href="{{ route('topics.index') }}"
                               title="Administrador de temas">
                                {{--<i class="fas fas fa-clone"></i>--}}
                                <i class="far fa-file-alt"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Temas</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.all')
                        <li id="nav-categorias" class="nav-item {{(Request::is('SentimentWord','SentimentWord/*'))?'active':''}}">
                            <a href="{{ route('SentimentWord.index') }}"
                               title="Administrador de palabras">
                                <i class="fas fa-sort-alpha-up"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Palabras</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.all')
                        <li id="nav-scrap" class="nav-item {{ (Request::is('SelectPage','ScrapCategory','scrapPost','ScrapSelectInbox','Category/*', 'TwitterScrap', 'TwitterScrap/*'))? 'active':'' }}">
                            <a data-toggle="collapse" href="#sideScrap" class="collapsed"
                               aria-expanded="false"
                               title="Administrador de consulta">
                                <i class="far fa-clipboard"></i>
                                <p class="elements label label-default my-first-tour" id="element5">Consultas</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideScrap" style="">
                                <ul class="nav nav-collapse">
                                    <li title="Consultá Facebook">
                                        <a data-toggle="collapse" href="#sideConsultaFacebook" class="collapsed"
                                           aria-expanded="false"
                                           title="Administrador de consulta de Facebook">
                                            <i class="fab fa-facebook-square"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Facebook</p>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse" id="sideConsultaFacebook" style="">
                                            <ul class="nav nav-collapse">
                                                <li>
                                                    <a href="{{ route('scrapsAll.selectPage') }}">
                                                        <span class="sub-item">General</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('scrapsCategoty.selectCategoria') }}">
                                                        {{-- LAS CATEGORIAS SERAN CONTENIDO--}}
                                                        {{--<span class="sub-item">Categorias</span>--}}
                                                        <span class="sub-item">Contenidos</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('scrapsPost.index') }}">
                                                        <span class="sub-item">Publicación</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('scrapsInbox.ScrapSelectInbox') }}">
                                                        <span class="sub-item">Inbox</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li title="Consultá Twitter">
                                        <a data-toggle="collapse" href="#sideConsultaTwitter" class="collapsed"
                                           aria-expanded="false"
                                           title="Administrador de consulta de Twitter">
                                            <i class="fab fa-twitter"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Twitter</p>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse" id="sideConsultaTwitter" style="">
                                            <ul class="nav nav-collapse">
                                                <li>
                                                    <a href="{{ route('twitterScrap.index') }}">
                                                        <span class="sub-item">General</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('scrapContent.index') }}">
                                                        {{-- LAS CATEGORIAS SERAN CONTENIDO--}}
                                                        <span class="sub-item">Contenidos</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                    @can('users.all')
                    <!-- Realiza scrap de todas las categoria / contenido -->
                        <li id="nav-categorias" class="nav-item {{(Request::is('contenido','Category/*', 'Twitter', 'Twitter/')) ?'active':''}}">
                            <a data-toggle="collapse" href="#sideContent" class="collapsed" aria-expanded="false"
                               title="Scraps">
                                <i class="fas fas fa-clone"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Contenidos</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideContent" style="">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('Category.index') }}" title="Administrador de Contenido de Facebook">
                                            <span class="sub-item">Facebook</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('twitter.index') }}" title="Administrador de Contenido de Twitter">
                                            <span class="sub-item">Twitter</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                    @can('users.all')
                        <li id="nav-estadisticas" class="nav-item {{(Request::is('ComparatorPage','SelectStatisticsInteraction','SelectStatisticsSubCategory','SelectStatisticsPage') ) ?'active':''}}">
                            <a data-toggle="collapse" href="#sideEstadisticas" class="collapsed" aria-expanded="false">
                                <i class="fas fa-signal"></i>
                                <p class="elements label label-default my-first-tour" id="element6">Estadísticas</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideEstadisticas" style="">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('Statistics.page') }}">
                                            <span class="sub-item">Páginas</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Statistics.subcategoria') }}">
                                            {{--DE SUBCATEGORIA A TEMA--}}
                                            {{--<span class="sub-item">Subcategoria</span>--}}
                                            <span class="sub-item">Temas</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Statistics.selectInteraction') }}">
                                            <span class="sub-item">Interacción</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Comparator.Megacategory') }}">
                                            <span class="sub-item">Comparador</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('users.topics')
                        <li id="nav-tema" class="nav-item {{(Request::is('ReportSubcategory','ReportSubcategory/*'))?'active':''}}">
                            <a href="{{ route('Report.Subcategory') }}"
                               title="Reporte de tema">
                                <i class="far fa-file-alt"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Reporte tema</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.sync_up')
                        <li id="nav-categorias" class="nav-item {{(Request::is('Sync_Up','Sync_Up/*'))?'active':''}}">
                            <a href="{{ route('sync_up.index') }}"
                               title="Sincronizar WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Sincronizar</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.competence')
                        <li id="nav-tema" class="nav-item {{(Request::is('Competence','Competence/*'))?'active':''}}">
                            <a href="{{ route('Competence.index') }}"
                            title="Páginas competidoras">
                            <i class="far fa-copy"></i>
                            <p class="elements label label-default my-first-tour" id="element3">Competencias</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.index')
                        <li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
                            <h4 class="text-section">Administrativo</h4>
                        </li>
                        <li id="nav-tema" class="nav-item {{(Request::is('ClassifyTopics','ClassifyTopics/*', 'ClassifyTwitter/*'))?'active':''}}">
                            <a data-toggle="collapse" href="#sideClasificar" class="collapsed"
                               aria-expanded="false"
                               title="Clasificar tema">
                                <i class="fas fa-align-left"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Clasificar</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideClasificar" style="">
                                <ul class="nav nav-collapse">
                                    <li title="Clasificar publicaciones Facebook">
                                        <a href="{{ route('ClassifyTopics.index') }}"
                                           title="Administrador de clasificaciones de Facebook">
                                            <i class="fab fa-facebook-square"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Facebook</p>

                                        </a>
                                    </li>
                                    <li title="Clasificar publicaciones Twitter">
                                        <a href="{{ route('ClassifyTwitter.index') }}" title="Administrador de clasificaciones de Twitter">
                                            <i class="fab fa-twitter"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Twitter</p>

                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('users.index')
                        <li id="nav-clasificaciones" class="nav-item {{ (Request::is('ClassificationWord','ClassificationWord/*','SentimentSubCategory','PageInbox','clasification/post','PageSentiment', 'Classification/*', 'Mentions/*'))? 'active':'' }}">
                            <a data-toggle="collapse" href="#sideClasificaciones" class="collapsed"
                               aria-expanded="false"
                               title="Administrador de clasificaciones">
                                <i class="fas fa-sort-numeric-up"></i>
                                <p class="elements label label-default my-first-tour" id="element5">Clasificaciones</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideClasificaciones" style="">
                                <ul class="nav nav-collapse">
                                    <li title="Clasificar publicaciones Facebook">
                                        <a data-toggle="collapse" href="#sideClasificacionesFacebook" class="collapsed"
                                           aria-expanded="false"
                                           title="Administrador de clasificaciones de Facebook">
                                            <i class="fab fa-facebook-square"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Facebook</p>
                                            <span class="caret"></span>
                                        </a>

                                        <div class="collapse" id="sideClasificacionesFacebook" style="">
                                            <ul class="nav nav-collapse">
                                                <li>
                                                    <a href="{{ route('ClassifyFeeling.pageSentiment') }}"
                                                       title="Clasificar comentarios">
                                                        <span class="sub-item">Comentarios</span>
                                                    </a>
                                                </li>
                                                <li title="Clasificar publicaciones">
                                                    <a href="{{ route('InfoPage.selectFanPage') }}">
                                                        <span class="sub-item">Publicaciones</span>
                                                    </a>
                                                </li>
                                                <li title="clasificar inbox">
                                                    <a href="{{ route('SentimentInbox.pageInbox') }}">
                                                        <span class="sub-item">Conversaciones</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('SentimentSub.sentimentSubCategory') }}">
                                                        {{--DE SUBCATEGORIA A TEMAs--}}
                                                        <span class="sub-item">Temas</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    {{--<a href="{{ route('ClassificationWord.index') }}" title="clasificar palabras">--}}
                                                    {{--<span class="sub-item">Palabras</span>--}}
                                                    {{--</a>--}}
                                                </li>
                                                {{--<li>--}}
                                                {{--<a href="{{ route('AdminSentiment_User.index') }}" title="Administrar sentimientos">--}}
                                                {{--<span class="sub-item">Administrar sentimentimientos</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}
                                                {{--<li>--}}
                                                {{--<a href="{{ route('Classification.Auto_Classification') }}" title="">--}}
                                                {{--<span class="sub-item">Autoclasificacion</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}


                                            </ul>
                                        </div>
                                    </li>
                                    <li title="Clasificar publicaciones Twitter">
                                        <a data-toggle="collapse" href="#sideClasificacionesTwitter" class="collapsed"
                                           aria-expanded="false"
                                           title="Administrador de clasificaciones de Twitter">
                                            <i class="fab fa-twitter"></i>
                                            <p class="elements label label-default my-first-tour" id="element5">Twitter</p>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse" id="sideClasificacionesTwitter" style="">
                                            <ul class="nav nav-collapse">
                                                <li>
                                                    <a href="{{ route('classificarionTwitter.indexComment') }}"
                                                       title="Clasificar comentarios">
                                                        <span class="sub-item">Comentarios</span>
                                                    </a>
                                                </li>
                                                <li title="Clasificar publicaciones">
                                                    <a href="{{ route('classificarionTwitter.select') }}">
                                                        <span class="sub-item">Publicaciones</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('classificarionTwitter.selectTopics') }}">
                                                        {{--DE SUBCATEGORIA A TEMAS--}}
                                                        <span class="sub-item">Temas</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('mention.indexPage') }}" title="Menciones de palabras">
                                                        <span class="sub-item">Menciones</span>
                                                    </a>
                                                </li>
                                                {{--<li>--}}
                                                {{--<a href="{{ route('AdminSentiment_User.index') }}" title="Administrar sentimientos">--}}
                                                {{--<span class="sub-item">Administrar sentimentimientos</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}
                                                {{--<li>--}}
                                                {{--<a href="{{ route('Classification.Auto_Classification') }}" title="">--}}
                                                {{--<span class="sub-item">Autoclasificacion</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}


                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('users.index')
                        <li id="nav-categorias" class="nav-item {{(Request::is('Cron','Cron/*'))?'active':''}}">
                            <a href="{{ route('Cron.index') }}"
                               title="Administrador de ejecución de página">
                                <i class="fas fa-cogs"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Ejecución página</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.index')
                        <li id="nav-categorias" class="nav-item {{(Request::is('Analysis','Analysis/*'))?'active':''}}">
                            <a href="{{ route('analysis.index') }}"
                               title="Análisis de páginas">
                                <i class="fas fa-info"></i>
                                <p class="elements label label-default my-first-tour" id="element3">Análisis de páginas</p>
                            </a>
                        </li>
                    @endcan

                    @can('users.index')
                        <li id="nav-estadisticas" class="nav-item {{(Request::is('MegacategoryReview','ReportMegacategory')) ?'active':''}}">
                            <a data-toggle="collapse" href="#sideReporte" class="collapsed" aria-expanded="false">
                                <i class="fas fa-file-alt"></i>
                                <p class="elements label label-default my-first-tour" id="element7">Reporte</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sideReporte" style="">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('Review.Megacategory') }}">
                                            <span class="sub-item">Revisión</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Report.Megacategory') }}">
                                            {{--DE CATEGORIA A CONTENIDO--}}
                                            <span class="sub-item">Contenido</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                    @can('users.index')
                        <li id="nav-notificacion" class="nav-item {{(Request::is('Telephone/*','Alerts','Bubles', 'StatusMessage')) ?'active':''}}">
                            <a data-toggle="collapse" href="#sidenotificacion" class="collapsed" aria-expanded="false">
                                <i class="icon-bell"></i>
                                <p class="elements label label-default my-first-tour" id="element8">Notificación</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="sidenotificacion" style="">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('alerts.index') }}">
                                            <span class="sub-item">Alertas</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Telephone.index') }}">
                                            <span class="sub-item">Número de teléfono </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('bubles.index') }}">
                                            <span class="sub-item">Reporte de burbujas </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('messageStatus.index') }}">
                                            <span class="sub-item">Estatus de mensajes</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                @endif

                @can('users.index')
                    <li id="nav-admin" class="nav-item {{(Request::is('admin/*')) ?'active':''}}" onclick="altershow()" title="Recursos de administrador">
                        <a data-toggle="collapse" href="#sideadmin" class="collapsed" aria-expanded="false">
                            <i class="fas fa-wrench"></i>
                            <div class="hiside" id="hiside">
                                <p class="elements label label-default my-first-tour" id="element9">Administrar</p>

                            </div>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="sideadmin" style="">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('app.index') }}">
                                        <span class="sub-item">Aplicación Facebook</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('apiWhatsapp.index') }}">
                                        <span class="sub-item">WAPIAD</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('users.index') }}">
                                        <span class="sub-item">Usuarios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('companies.index') }}">
                                        <span class="sub-item">Compañias</span>
                                    </a>
                                </li>
                                @can('roles.index')
                                    <li>
                                        <a href="{{ route('roles.index') }}">
                                            <span class="sub-item">Roles</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('permissions.index')
                                    <li>
                                        <a href="{{ route('permissions.index') }}">
                                            <span class="sub-item">Permisos</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
        });

        function altershow() {
            document.getElementById('hiside').style.display = 'block';

        }
    </script>
@endsection
