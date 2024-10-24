<!doctype html>
@if(!empty(\Session::get('lang')))
    @php
        \App::setLocale(Session::get('lang'));
    @endphp
@else
    @php
        \App::setLocale('ar');
    @endphp
@endif
<html lang="ar" dir="rtl">

<head>
    <title>My Identity Assessment | @yield('title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="{{asset('web_assets/css/bootstrap.rtl.min.css')}}" rel="stylesheet">
    <link href="{{asset('web_assets/css/custom.css')}}?v=2" rel="stylesheet">
    <link href="{{asset('web_assets/css/responsive.css')}}?v=2" rel="stylesheet">
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" />
</head>

<body>

<main class="login-page science">
    @yield('content')
</main>


<script src="{{asset('web_assets/js/bootstrap.min.js')}}"></script>
<script src="{{asset('web_assets/js/toastify.js')}}"></script>
<script src="{{asset('web_assets/js/custom.js')}}"></script>
@yield('script')

</body>
</html>
