<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Toy Rental - Sistema</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }

        .loader-full {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
            z-index: 9990;
            display: none;

        }

        .loader {
            position: fixed;
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body id="app-layout">
    <div class="loader-full" id="loader">
        <div class="loader"></div>
    </div>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <!-- <span><img width="96px" src="/images/logo.jpeg"></img></span> -->
                <a class="navbar-brand" href="{{ url('/sistema') }}">
                    <span><img style="max-width:84px; margin-top: -9px;"
                        src="/images/logo.jpeg"></span>
                </a>
            </div>
            
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (!Auth::guest() && !Auth::user()->permissions()->get()->whereIn('name', ['relatorio', 'shopping'])->first())
                        @if (!Auth::guest() && !Auth::user()->kiosk_id)
                            <li><a href="{{ url('/cadastro') }}">Cadastros</a></li>
                        @endif
                        <li><a href="{{ url('/tutorials') }}">Tutorial</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Relatórios<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/report/cash') }}">Caixa</a></li>
                                <li><a href="{{ url('/report') }}">Aluguéis</a></li>
                                <li><a href="{{ url('/report/employes') }}">Funcionários</a></li>
                                <li><a href="{{ url('/report/toys') }}">Brinquedos</a></li>
                                <li><a href="{{ url('/report/payment-way') }}">Forma de pagamento</a></li>
                                <li><a href="{{ url('/report/entry-exit') }}">Lançamentos</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <!-- <li><a href="{{ url('/register') }}">Registro</a></li> -->
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/user') }}"><i class="fa fa-btn fa-key"></i>Trocar senha</a></li>
                                <li><a href="{{ url('/report/cash/close') }}"><i class="fa fa-btn fa-sign-out"></i>Sair</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{!! asset('js/moment.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/jquery.mask.js') !!}"></script>
    

    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    <script>
        function showLoader(){
            $("#loader").show();
        }
        
        function hideLoader(){
            $("#loader").hide();
        }

        function showError(error, status, xhr){
            if(xhr && xhr.status === 401){
                location.reload();
                return;
            }
            hideLoader();
            if(xhr.responseText){
                alert(xhr.responseText);
            } else {
                if(xhr.status){
                    alert(xhr.status + ' = ' + error);
                }
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
