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
    @include('layouts.theme.header')
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