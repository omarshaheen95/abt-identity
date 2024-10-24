<!doctype html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale()!= 'ar'?'ltr':'rtl'}}">
<head>
    <title>My Identity Assessment | @yield('title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.0.2 -->
    <link rel="stylesheet" href="{{asset('assets_v1/lib/bootstrap-5.0.2/css/bootstrap.rtl.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets_v1/auth_css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets_v1/auth_css/responsive.css')}}">
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" />
    @if(app()->getLocale() == "ar")
        <style>
            html, body ,.title{
                font-family: Almarai, "sans-serif";
            }
        </style>
    @endif
    @yield('style')

</head>
<body>

@yield('content')
<!-- Bootstrap JavaScript Libraries -->
<script src="{{asset('web_assets/js/jquery-3.6.3.min.js')}}"></script>
<script src="{{asset('web_assets/js/popper-2.9.2.min.js')}}"></script>
<script src="{{asset('assets_v1/lib/bootstrap-5.0.2/js/bootstrap.min.js')}}"></script>
<script src="{{asset('web_assets/js/custom.js')}}"></script>
<script src="{{asset('assets_v1/js/browserInfo.js')}}"></script>
@yield('script')

</body>
</html>
