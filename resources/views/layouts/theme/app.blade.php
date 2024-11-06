<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>SISTEMA ECOMMERCE</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/llantas.png') }}"/>

    <!--INICIO DE ESTILOS GLOBALES-->
    @include('layouts.theme.styles')
    <!--FIN DE ESTILOS GLOBALES-->

</head>
<body class="sidebar-noneoverflow dashboard-sales">
    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <!--INICIO DEL HEADER/NAVBAR-->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">
            <ul class="navbar-item flex-row">
                <li class="nav-item theme-logo">
                    <a href="{{ url('home') }}">
                        <img src="assets/img/llantas.jpg" class="navbar-logo" alt="logo">
                        <b style="font-size: 19px; color:#3B3F5C">CAPRILES</b>
                    </a>
                </li>
            </ul>
    
            <div id="compact_submenuSidebar" class="submenu-sidebar">   <!--div agregado para el sidebar collapse-->
                <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list">
                <line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3" y2="6"></line><line x1="3" y1="12" x2="3" y2="12"></line><line x1="3" y1="18" x2="3" y2="18"></line></svg></a>
            </div>
            
            {{--<livewire:search>   <!--inclucion de caja de busqueda-->--}}
    
            <ul class="navbar-item ml-auto">
                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-user text-dark"></i>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                {{--imagen dinamica de usuario con validacion de si tiene imagen--}}
                                
                                <img src="{{ Auth::user()->image != null ? asset('storage/users/' . Auth::user()->image) : asset('storage/noimg.jpg') }}" class="img-fluid mr-2" alt="avatar">
                                <div class="media-body">
                                    <h5>{{Auth::user()->name}}</h5>{{--nombre dinamico de usuario--}}
                                    <p>{{Auth::user()->profile}}</p>{{--perfil dinamico de usuario--}}
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path 
                                d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12">
                                </line></svg> <span>Salir</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--FIN DEL HEADER/NAVBAR-->

    <!--INICIO DEL CONTENEDOR PRINCIPAL-->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--INICIO DEL SIDEBAR-->
        @include('layouts.theme.sidebar')
        <!--FIN DEL SIDEBAR-->
        
        <!--INICIO DEL AREA DE CONTENIDO-->
        <div id="content" class="main-content">

            <div class="layout-px-spacing">

                @yield('content')   {{--SECCION A SER RENDERIZADA--}}

            </div>

            <!--INICIO DEL FOOTER-->
            @include('layouts.theme.footer')
            <!--FIN DEL FOOTER-->

        </div>
        <!--FIN DEL AREA DE CONTENIDO-->

    </div>
    <!--FIN DEL CONTENEDOR PRINCIPAL-->

    <!--INICIO DE SCRIPTS GLOBALES-->
    @include('layouts.theme.scripts')
    <!--FIN DE SCRIPTS GLOBALES-->

</body>
</html>