<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Short Report Card for Students" name="description"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets_v1/plugins/bootstrap-5.0.2/css/bootstrap.rtl.css') }}" rel="stylesheet"
              type="text/css"/>
    @else
        <link href="{{ asset('assets_v1/plugins/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet"
              type="text/css"/>
    @endif
    <link href="{{ asset('assets_v1/plugins/print/css/print.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets_v1/plugins/print/css/report.css') }}?v={{time()}}" rel="stylesheet" type="text/css"/>
    <title>{{ re('The Student Report') }} - {{ $student->name }}</title>
    <style>
        .table td, .table th {
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="subpage-w">
        <!-- محتوى التقرير -->
        <div class="row align-items-center">
            <div class="col-6 text-center">
                <img
                    src="{{!settingCache('logo') ? asset('logo.svg') : asset(settingCache('logo'))}}?v={{time()}}"
                    width="75%" style="max-height: 100px;" alt="">
            </div>
            <div class="col-6">
                <h3 class="main-color">{{ re('Student Report Card') }}</h3>
                <span style="font-weight: 500">
                    {{ re('Created On') }}: {{ date('Y-m-d') }}
                </span>
            </div>
        </div>
        <hr class="mt-2"/>
        <div class="row mt-3 justify-content-center">
            <div class="col-8 text-center">
                <div class="table-container">
                    <table class="table m-0">
                        <tr>
                            <td class="main-td">{{ re('Student Name') }}</td>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <td class="main-td">{{ re('School') }}</td>
                            <td>{{ $student->school->name }}</td>
                        </tr>
                        <tr>
                            <td class="main-td">{{ re('Level') }}</td>
                            <td>{{ $student->level->name }}</td>
                        </tr>
                        <tr>
                            <td class="main-td">{{ re('Section') }}</td>
                            <td>{{ $student->grade_name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="image-container">
                    <img src="{{ asset($student->school->logo) }}?v={{time()}}" alt="">
                </div>
            </div>
        </div>

        <!-- Skills Results Table -->
        <div class="row mt-3 text-center">
            <div class="col-12">
                <h5 class="section-title">{{ re('The Skills Results') }}</h5>
            </div>
        </div>
        <div class="row text-center justify-content-center mt-1">
            <div class="col-12">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{ re('The assessment') }}</th>
                            @foreach($subjects as $subject)
                                <th class="main-th">{{ re($subject->name) }}</th>
                            @endforeach

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($student_terms as $d_term)
                            <tr>
                                <td>{{ $d_term->term->round }} {{ re('Round') }}</td>
                                @foreach($subjects as $subject)
                                    @php
                                        $skill_mark = collect($d_term->subjects_marks)->firstWhere('subject_id', $subject->id)['mark'];
                                        $category = $subject->getCategoryForMark($skill_mark)
                                    @endphp
                                    <td>
                                        <span
                                            class="text-{{strtolower($category)}}">{{ $skill_mark }}</span>
                                        / {{$subject->mark}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overall Results Table -->
        <div class="row mt-3 text-center">
            <div class="col-12">
                <h5 class="section-title">{{ re('Overall Results') }}</h5>
            </div>
        </div>
        <div class="row mt-1 text-center justify-content-center">
            <div class="col-12">
                <div class="table-container">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th class="main-th">{{ re('The assessment') }}</th>
                            <th class="main-th">{{ re('Mark') }}</th>
                            <th class="main-th">{{ re('Attainment') }}</th>
                            <th class="main-th">{{ re('Progress') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($student_terms as $d_term)
                            <tr>
                                <td>{{ $d_term->term->round }} {{ re('Round') }}</td>
                                <td>{{ $d_term->total }} / 100</td>
                                <td class="{{ strtolower($d_term->expectations) }}-td"><span
                                        class="">{{ re($d_term->expectations) }}</span></td>
                                <td><span
                                        class="{{ strtolower($d_term->progress_class) }}-badge">{{ re($d_term->progress) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="footer">
        <img src="{{ asset('assets_v1/media/reports/footer-logos.svg') }}?v={{time()}}" width="100%" alt="">
    </div>
</div>
</body>
</html>
