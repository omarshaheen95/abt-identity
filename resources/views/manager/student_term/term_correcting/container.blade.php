<!doctype html>
@if(isset($term))
    <html lang="{{$term->level->arab?'ar':'en'}}" dir="{{$term->level->arab?'rtl':'ltr'}}">
    @else
    <html lang="{{app()->getlocale()}}" dir="{{direction()}}">
@endif
<head>
    <meta charset="UTF-8">
    <title> A.B.T | @yield('title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" />
    @yield('pre-style')
    @if(isset($term))
        @if($term->level->arab)
            <link href="{{asset('web_assets/css/bootstrap.rtl.min.css')}}" rel="stylesheet">
        @else
            <link href="{{asset('web_assets/css/bootstrap.min.css')}}" rel="stylesheet">
        @endif
    @else
        <link href="{{asset('web_assets/css/bootstrap.rtl.min.css')}}" rel="stylesheet">
    @endif
    <link href="{{asset('web_assets/css/custom.css')}}?v={{time()}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/custom2.css')}}?v1" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/arabic-keyboard.css')}}">
    <style>
        #keyboardInputLayout {
            direction: ltr !important;
        }

        #keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td {
            font: normal 30px 'Lucida Console', monospace;
        }

        .keyboardInputInitiator {
            width: 50px
        }


    </style>

    @yield('style')

</head>
<body>
<main class="exam-page science">

<nav class="navbard-top">
    <div class="container">
        <div class="navbar-container">
            <div class="logo">
                <a href="{{route('student.home')}}">
                        <img alt="Logo" src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" style="width: 140px;"/>
                </a>
            </div>
            <div class="action">
{{--                @if(app()->getLocale() == "ar")--}}
{{--                    <a href="{{ route('switch-language', 'en') }}" class="lang">--}}
{{--                        <span class="text me-2 d-none d-sm-inline-block"> English </span>--}}
{{--                        <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-states.svg')}}" width="20px" alt="arabic">--}}
{{--                    </a>--}}
{{--                @else--}}
{{--                    <a href="{{ route('switch-language', 'ar') }}" class="lang">--}}
{{--                        <span class="text me-2 d-none d-sm-inline-block"> العربية </span>--}}
{{--                        <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-arab-emirates.svg')}}" width="20px" alt="arabic">--}}
{{--                    </a>--}}
{{--                @endif--}}

                <div class="dropdown dropdown-profile">
                    <a class="dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="profile-card">
                            <div class="pic">
                                <img src="{{asset('web_assets/img/user.svg')}}" alt="">
                            </div>
                            <div class="content d-none d-sm-inline-block d-flex align-items-center ">
                                @if(isset($student->name))
                                    <h2 class="name">{{$student->name}} </h2>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
@yield('navbar')

@yield('content')

</main>

@yield('pre-script')
<script src="{{asset('web_assets/js/jquery-3.6.3.min.js')}}"></script>
<script src="{{asset('web_assets/js/popper.min.js')}}"></script>
<script src="{{asset('web_assets/js/bootstrap.min.js')}}"></script>
<script src="{{asset('web_assets/js/toastify.js')}}"></script>
<script src="{{asset('web_assets/js/jquery.countdown.min.js')}}"></script>
<script src="{{asset('web_assets/js/jquery-ui.js')}}"></script>
<script src="{{asset('web_assets/js/custom.js')}}"></script>
<script src="{{asset('web_assets/js/jquery.ui.touch-punch.js')}}"></script>
<script src="{{asset('calculator_assets/js/script.js')}}"></script>
<script src="{{asset('web_assets/js/student_term.js')}}?v=11"></script>

@yield('script')

</body>
</html>
