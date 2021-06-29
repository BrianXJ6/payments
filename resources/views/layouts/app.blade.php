<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>{{ config('app.name') }} - @yield('subtitle')</title>
    <meta name="DC.title" content="{{ config('app.name') }} - @yield('subtitle')" />
    <meta property="og:title" content="{{ config('app.name') }} - @yield('subtitle')" />
    <meta name="description" content="@yield('description')">
    <meta property="og:description" content="@yield('description')" />
    <meta name="keywords" content="@yield('keywords')">
    <meta name="author" content="Brian Ferreira">
    <meta name="abstract" content="@yield('description')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="@yield('cover')">
    <meta name="robots" content="index, follow" />
    <meta name="googlebot" content="INDEX, FOLLOW" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ mix('js/manifest.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="h-100">
    <div id="app" class="h-100 d-flex flex-column">
        <header class="flex-shrink-0">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-lg">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="main-navbar">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Início</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle pointer" data-toggle="dropdown">Gerencianet</a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('gn.cart') }}">Pagamentos via cartão</a>
                                    <a class="dropdown-item" href="{{ route('gn.billet') }}">Pagamentos via boleto</a>
                                    <a class="dropdown-item" href="{{ route('gn.pix') }}">Pagamentos via PIX</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="flex-shrink-0">
            @yield('content')
        </main>
        <footer class="mt-auto small bg-dark text-light shadow-lg-inverse py-2">
            <div class="container small">
                <span>Integração com sistemas de pagamentos</span>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
