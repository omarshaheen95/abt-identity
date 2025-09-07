<!DOCTYPE html>
<html lang="{{app()->getlocale()}}" dir="{{app()->getLocale()=='ar'?'rtl':'ltr'}}">
@php
    $lang = app()->getLocale()=='ar'?'ar':'en';
@endphp
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="نظام اختبارات وتقييم للطلاب للدراسات الإجتماعية S.S.B.T – Social Studies Benchmark Test" name="description"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets_v1/lib/bootstrap-5.0.2/css/bootstrap.rtl.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/print.rtl.css') }}?v=4" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.rtl.css') }}?v=4" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{ asset('assets_v1/lib/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/print.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets_v1/plugins/print/css/report.css') }}?v=3" rel="stylesheet" type="text/css"/>
    @endif

    <link href="{{ asset('assets_v1/plugins/fontawesome-6.1/css/all.min.css') }}" rel="stylesheet" type="text/css"/>

    <link rel="shortcut icon"
          href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}"/>
    <script src="{{ asset('assets_v1/plugins/print/js/new_highcharts.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets_v1/plugins/print/js/rounded-corners.js') }}"></script>
    <title>{{$title}}</title>
    @stack('style')
</head>

<body>

@yield('content')

<script src="{{ asset('web_assets/js/jquery-3.6.3.min.js') }}" type="text/javascript"></script>
@stack('script')
</body>


</html>
