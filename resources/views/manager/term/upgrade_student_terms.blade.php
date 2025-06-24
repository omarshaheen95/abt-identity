@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.student_term.index',['status'=>'uncorrected'])}}" class="text-muted">
            {{t('Uncorrected Terms')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <div class="row">
        <form class="kt-form kt-form--fit mb-15" id="form_student_term_data"
              method="POST" action="{{route('manager.students-terms.upgrade-student-term')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row justify-content-center">
                <div class="col-lg-4 mb-3">
                    <label class="form-label">{{t('Select School')}}</label>
                    <select name="school_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select School')}}">
                        <option value="" disabled selected>{{t('Select School')}}</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">{{t('Select Year')}}</label>
                    <select name="year_id" class="form-control form-select" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select Year')}}">
                        <option value="" disabled selected>{{t('Select Year')}}</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach

                    </select>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">{{t('Select Round')}}</label>
                        <select name="month" class="form-control form-select" data-control="select2"
                                data-allow-clear="true" data-placeholder="{{t('Select Round')}}">
                            <option value="" disabled selected>{{t('Select Round')}}</option>
                                <option value="september">September</option>
                                <option value="february">February</option>
                                <option value="may">May</option>
                        </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">{{t('Select Type')}}</label>
                        <select name="arab" id="section" class="form-control form-select" data-control="select2"
                                data-placeholder="{{t('Select Type')}}">
                            <option></option>
                            <option value="2">{{t('Arabs And Non-Arabs')}}</option>
                            <option value="1">{{t('Arabs')}}</option>
                            <option value="0">{{t('Non-Arabs')}}</option>
                        </select>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">{{t('Select Grade')}}</label>
                        <select class="form-control form-select" multiple data-control="select2" data-allow-clear="true"
                                data-placeholder="{{t('Select Grade')}}" name="grades[]">
                            <option></option>
                            @foreach($grades as $grade)
                                <option
                                    value="{{$grade}}">{{$grade}}</option>
                            @endforeach
                        </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">{{t('Updated At')}}</label>
                    <input type="text" name="update_date" class="form-control" id="update_date" placeholder="{{t('Update Date')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('Updated Operator')}}</label>
                    <input type="text" name="update_operator" class="form-control" id="update_operator" placeholder="{{t('Updated Operator')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('From Result')}}</label>
                    <input type="number" name="from_total_result" class="form-control" id="from_total_result" placeholder="{{t('From Result')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('To Result')}}</label>
                    <input type="number" name="to_total_result" class="form-control" id="to_total_result" placeholder="{{t('To Result')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('Select Process')}}</label>
                    <select name="process_type" id="process_type" class="form-control form-select" data-control="select2"
                            data-placeholder="{{t('Select Process')}}">
                        <option></option>
                        <option value="upgrade">{{t('upgrade')}}</option>
                        <option value="downgrade">{{t('downgrade')}}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('Mark')}}</label>
                    <input type="number" name="mark" class="form-control" id="mark" placeholder="{{t('Mark')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('Range Result')}}</label>
                    <input type="number" name="range_mark" class="form-control" id="range_mark" placeholder="{{t('Range Mark')}}">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">{{t('Check')}}</label>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault" name="check_counts"/>
                        <label class="form-check-label" for="flexSwitchDefault">
                            {{t('Check Counts')}}
                        </label>
                    </div>
                </div>
            </div>
            <div class="row my-5">
                <div class="col-12 d-flex justify-content-end">
                    <button id="check_count" type="submit"
                            class="btn btn-primary mr-2">{{t('Execute')}}</button>
                </div>
            </div>

        </form>


    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\UpgradeStudentTermRequest::class, '#upload_students_file'); !!}
    <script>
        $("#update_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
        $('#check_count').on('click', function (e){
            e.preventDefault();
            var form = $('#form_student_term_data');
            //check if check_counts is checked
            if ($('#flexSwitchDefault').is(':checked')) {
                var title = "{{t('Check Students Count')}}";
                var sub_title = "{{t('Get Students Terms Count According To Your Filters')}}";
            }else{
                var title = "{{t('Execute Process')}}";
                var sub_title = "{{t('Are You Sure You Want To Execute Process According To Your Filters')}}";
            }
            //Swal.fire
            Swal.fire({
                title: title,
                text: sub_title,
                confirmButtonColor: '#ff091d',
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{t('Ok')}}",
                cancelButtonText: "{{t('Cancel')}}",
            }).then(function (result) {
                console.log(form.attr('action'));
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: form.attr('action'), // get the route value
                        data: form.serialize(),
                    }).done(function (data) {
                        console.log(data)
                        if (data.success) {
                            Swal.fire(
                                "Students Terms Count",
                                data.data,
                                "success"
                            )
                        } else {
                            Swal.fire(
                                data.data,
                                data.data,
                                "error"
                            )
                        }
                    });

                    console.log('test');

                }

            });

        })
    </script>
@endsection
