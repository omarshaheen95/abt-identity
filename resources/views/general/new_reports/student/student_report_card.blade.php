@extends('general.new_reports.layout')
@push('style')
@endpush

@section('content')
    <div class="page">
        <div class="subpage-w">
            <!-- محتوى التقرير -->
            <div class="row align-items-center">
                <div class="col-3 text-center">
                    <img src="{{asset('logo_circle.svg')}}" width="100%" style="max-height: 150px;" alt="">
                </div>

                <div class="col-6 text-center">
                    <h5 class="section-title"><i class="fas fa-chart-bar me-2"></i> {{ re('Student Report Card') }}</h5>
                    {{--                <h3 class="main-color"></h3>--}}
                    <span style="font-weight: 500">
                    <i class="fas fa-calendar-alt me-1"></i> {{ re('Created On') }}: {{ date('Y-m-d') }}
                </span>
                </div>
                <div class="col-3 text-center">
                    {!! QrCode::color(245,87,87)->size(120)->generate(sysDomain()."/student-report-card?token=".encryptStudentId($student->id)); !!}
                </div>
            </div>
            <hr class="mt-2"/>
            <div class="row mt-3 justify-content-center">
                <div class="col-8 text-center">
                    <div class="table-container">
                        <table class="table m-0">
                            <tr>
                                <td class="main-td"><i class="fas fa-user-graduate me-2"></i>{{ re('Student Name') }}</td>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <td class="main-td"><i class="fas fa-school me-2"></i>{{ re('School') }}</td>
                                <td>{{ $student->school->name }}</td>
                            </tr>
                            <tr>
                                <td class="main-td"><i class="fas fa-layer-group me-2"></i>{{ re('Level') }}</td>
                                <td>{{ $student->level->short_name }}</td>
                            </tr>
                            <tr>
                                <td class="main-td"><i class="fas fa-users me-2"></i>{{ re('Section') }}</td>
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
                    <h5 class="section-title"><i class="fas fa-chart-bar me-2"></i>{{ re('The Skills Results') }}</h5>
                </div>
            </div>
            <div class="row text-center justify-content-center mt-1">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table m-0 small">
                            <thead>
                            <tr>
                                <th class="main-th"><i class="fas fa-calendar-alt me-1"></i>{{ re('The assessment') }}</th>
                                @foreach($subjects as $subject)
                                    <th class="main-th">{{re($subject->name) }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_terms as $d_term)
                                <tr>
                                    <td>{{ $d_term->month }}</td>
                                    @foreach($subjects as $subject)
                                        @if($d_term->{"mark_step$subject->id"})
                                            <td><span>{{ $d_term->{"mark_step$subject->id"} }}</span></td>
                                        @else
                                            <td>N/A</td>
                                        @endif
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
                    <h5 class="section-title"><i class="fas fa-chart-pie me-2"></i>{{ re('Overall Results') }}</h5>
                </div>
            </div>
            <div class="row mt-1 text-center justify-content-center">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th class="main-th"><i class="fas fa-clipboard-check me-2"></i>{{re('The assessment')}}</th>
                                <th class="main-th"><i class="fas fa-percentage me-2"></i> {{re('Mark')}}</th>
                                <th class="main-th"><i class="fas fa-gavel me-2"></i> {{re('Attainment')}}</th>
                                <th class="main-th"><i class="fas fa-chart-line me-2"></i> {{re('Progress')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_terms as $d_term)
                                <tr>
                                    <td>{{ $d_term->month }} {{ re('Round') }}</td>
                                    <td>{{ $d_term->total }} / 100</td>
                                    <td><span class="{{$d_term->css_class}}-badge">{{ $d_term->expectation }}</span>
                                    </td>
                                    <td><span class="{{ strtolower($d_term->progress_class) }}-badge">{{ re($d_term->progress) }}</span></td>
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
            {{--        <img src="{{ asset('assets_v1/media/reports/footer-logos.svg') }}?v={{time()}}" width="100%" alt="">--}}
        </div>
    </div>
@endsection
