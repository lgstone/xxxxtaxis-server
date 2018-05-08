<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            <!-- <li><a class="nav-link" href="{{ route('register') }}">Register</a></li> -->
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        
        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    @if(strpos($_SERVER['REQUEST_URI'], "channel") != false)
                    <div class="col-md-12">
                        <div class="card card-default">
                            <div class="card-header">
                                <ul class="nav nav-pills">
                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/passenger')}}" href="/channel/passenger">Rider</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/driver')}}" href="/channel/driver">Driver</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/vehicle')}}" href="/channel/vehicle">Vehicle</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/trip')}}" href="/channel/trip">Trip</a>
                                  </li>

                                  <li class="nav-item">
                                    <a class="nav-link disabled" href="#">|</a>
                                  </li>

                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/driverRegister')}}" href="/channel/driverRegister">Driver Application</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link {{Helper::set_active('channel/driverApplyHistory')}}" href="/channel/driverApplyHistory">Application History</a>
                                  </li>

                                </ul>
                            </div>

                            @yield('content')

                        </div>
                    </div>
                    @else
                        @yield('content')
                    @endif
                </div>
            </div>
        

        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>


@yield('scripts')