@if($student->level->arab)
@php
    app()->setLocale('ar')
@endphp
@else
@php
    app()->setLocale('en')
@endphp
@endif
<!doctype html>
<html lang="{{$student->level->arab?'ar':'en'}}" dir="{{$student->level->arab?'rtl':'ltr'}}">
<head>
    <meta charset="UTF-8">
    <title>My Identity Assessment | @yield('title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" />

    @yield('pre-style')
    @if($student->level->arab)
        <link href="{{asset('web_assets/css/bootstrap.rtl.min.css')}}" rel="stylesheet">
    @else
        <link href="{{asset('web_assets/css/bootstrap.min.css')}}" rel="stylesheet">
    @endif
    <link href="{{asset('web_assets/css/custom.css')}}?v={{time()}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/responsive.css')}}?v={{time()}}" rel="stylesheet">
    @yield('style')

</head>
<body onbeforeunload="">
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
{{--                        <span class="text me-2 d-none d-sm-inline-block"> العربية </span>--}}
{{--                        <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-arab-emirates.svg')}}" width="20px" alt="arabic">--}}
{{--                    </a>--}}

{{--                @else--}}
{{--                    <a href="{{ route('switch-language', 'ar') }}" class="lang">--}}
{{--                        <span class="text me-2 d-none d-sm-inline-block"> English </span>--}}
{{--                        <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-states.svg')}}" width="20px" alt="arabic">--}}
{{--                    </a>--}}
{{--                @endif--}}

                <div class="dropdown dropdown-profile">
                    <a class="dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="profile-card">
                            <div class="pic">
                                <img src="{{asset('web_assets/img/user.svg')}}" alt="">
                            </div>
                            <div class="content d-none d-sm-inline-block d-flex align-items-center ">
                                <h2 class="name">{{auth()->guard('student')->user()->name}} </h2>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="triggerId">
                        <form id="logout-form" action="{{ url('/student/logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>

                        <a class="dropdown-item" href="{{ url('/student/logout') }}"
                           onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();"
                        > Logout </a>
                    </div>
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


@yield('script')

</body>
</html>
