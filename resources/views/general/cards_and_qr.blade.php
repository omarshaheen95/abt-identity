<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ asset('assets_v1/lib/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets_v1/print/css/print.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets_v1/print/css/student_card.css') }}?v={{time()}}" rel="stylesheet" type="text/css" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" />

    <title>{{$title}}</title>
    <style>

    </style>
</head>
<body>
@php
    $count = 0;

@endphp
@foreach($students as $student)
    <div class="page">
        <div class="subpage-w">
            <div class="row">
                @foreach($student as $std)
                    <div class="col-6 p-1">
                        <div class="p-3 student-card">
                            <div class="d-flex flex-column bg-white p-2 h-100" style="border-radius: 8px">
                                <div class="row py-2">
                                    <div class="col-4 text-center">
                                        <div class="image-container-cards">
                                            <img class="logo" src="{{ asset(optional($std->school)->logo) }}"/>
                                        </div>
                                    </div>
                                    <div class="col-8 d-flex flex-column align-items-center mt-1 " style="font-size: 12px">
                                        <div class="fw-bold text-center">{{ optional($std->school)->name }}</div>
                                        <div class="fw-bold">Student Login</div>
                                    </div>
                                </div>
                                <hr class="my-1" style="border-top: 1px solid #00000040;">
                                <div class="row mt-1 px-1">
                                    <div class="col-7 d-flex flex-column pe-0">
                                        <div class="col-12 s-content"><span class="s-title">Name : {{ $std->name }}</span></div>
                                        <div class="col-12 s-content"><span class="s-title">ID : </span>{{ $std->id_number ?? '-' }}</div>
                                        <div class="col-12 s-content"><span class="s-title">Grade : </span>Grade {{ $std->level->grade }}</div>
                                        <div class="col-12 s-content"><span class="s-title"> Section : </span>{{ $std->grade_name ?? '-' }}</div>
                                        <div class="col-12 s-content"><span class="s-title "> Arab Status : </span>
                                            @if($std->level->arab)
                                                <span class="arabs">Arabs</span>
                                            @else
                                                <span class="non-arabs">Non-Arabs</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-5 mt-1 d-flex justify-content-end p-0 pe-1">
                                        @if($std->gender == 'boy')
                                            {!! QrCode::color(0, 166, 255)->size(100)->generate(sysDomain()."/student/login?username=".$std->email); !!}
                                        @elseif($std->gender == 'girl')
                                            {!! QrCode::color(255, 0, 194)->size(100)->generate(sysDomain()."/student/login?username=".$std->email); !!}
                                        @else
                                            {!! QrCode::color(197, 65, 141)->size(100)->generate(sysDomain()."/student/login?username=".$std->email);!!}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-1 px-1">
                                    <div class="col-12 d-flex flex-column gap-1 pe-0">
                                        <div class="s-title">
                                            <ul>
                                                <li>{{sysDomain()}}</li>
                                                <li>Student login</li>
                                                <li>Username: <span class="username" >{{ $std->email}}</span></li>
                                                <li>Login</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


        </div>
    </div>
@endforeach
</body>
</html>
