@extends('school.layout.container')

@section('title')
    {{t('Add Marking Request')}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{t('Add Marking Request')}}</li>
@endpush
@section('content')
    <div class="row">
        <form id="form_information"
              action="{{ isset($marking_request) ? route('school.marking_requests.update', $marking_request->id): route('school.marking_requests.store') }}"
              method="post">
            @csrf

            @if(isset($marking_request))
                <input type="hidden" name="_method" value="patch">
            @endif

            <div class="row gap-3">
                <div class="col-12 mt-1">
                    <label class="form-label">{{t('Year')}}:</label>
                    <select class="form-select" required name="year_id" id="year_id" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select year')}}">
                        <option></option>
                        @foreach($years as $year)
                            <option value="{{$year->id}}"
                                    @if(isset($marking_request) && $marking_request->year_id==$year->id)selected @endif>{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mt-1">
                    <label class="form-label">{{t('Round')}}:</label>
                    <select class="form-select" required name="round" id="round" data-control="select2"
                            data-allow-clear="true" data-placeholder="{{t('Select year')}}">
                        @foreach(['september','may','february'] as $month)
                            <option value="{{$month}}"
                                    @if(isset($marking_request) && $marking_request->month==$month)selected @endif>{{$month}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">{{t('Section')}} :</label>
                    <div class="d-flex border-secondary p-4" style="border: 1px solid;border-radius: 5px;">
                        <div class="form-check form-check-custom form-check-solid me-3">
                            <input class="form-check-input" name="section" type="radio" value="0" id="section"
                                   @if(isset($marking_request) && $marking_request->section==0)checked
                                   @else checked @endif/>
                            <label class="form-check-label" for="section">
                                {{t('All')}}
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid me-3">
                            <input class="form-check-input" name="section" type="radio" value="1" id="section"
                                   @if(isset($marking_request) && $marking_request->section==1)checked @endif/>
                            <label class="form-check-label" for="section">
                                {{t('Arab')}}
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" name="section" type="radio" value="2" id="section"
                                   @if(isset($marking_request) && $marking_request->section==2)checked @endif/>
                            <label class="form-check-label" for="section">
                                {{t('Non-Arabs')}}
                            </label>
                        </div>

                    </div>
                </div>

                <div class="col-12 ">
                    <label class="form-label"> {{t('Grades')}}: </label>
                    <div class="col-12 row p-4 border-secondary ms-1" style="border: 1px solid;border-radius: 5px">

                        <div class=" col-12 form-check form-check-custom form-check-solid form-check-sm mb-1 ">
                            <input class="form-check-input grade" type="checkbox" name="all_grades" value="all_grades"
                                   id="flexRadioLg"
                                   @if(isset($marking_request) && count($marking_request->grades)==12)checked
                                   @elseif(!isset($marking_request)) checked @endif/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('All')}}
                            </label>
                        </div>

                        @for($i=1;$i<=12;$i++)
                            <div class="col-2 form-check form-check-custom form-check-solid form-check-sm mb-1">
                                <input class="form-check-input grade" type="checkbox" name="grades[]" value="{{$i}}"
                                       id="flexRadioLg"
                                       @if(isset($marking_request) && in_array($i,$marking_request->grades))checked
                                       @elseif(!isset($marking_request)) checked @endif/>
                                <label class="form-check-label" for="flexRadioLg">
                                    {{t('Grade')}} {{$i}}
                                </label>
                            </div>
                        @endfor

                    </div>

                </div>

                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label class="form-label">{{t('Email')}}</label>
                        <input class="form-control" name="email" type="text"
                               value="{{isset($marking_request)?$marking_request->email:''}}">
                    </div>
                </div>

                <div class="col-12 mb-2">
                    <label for="" class="form-label">{{t('Notes')}}</label>
                    <textarea class="form-control" data-kt-autosize="true"
                              name="notes">{{isset($marking_request)?$marking_request->notes:''}}</textarea>
                </div>

                <div class="col-12 mb-5">
                    <div class="form-check form-check-custom form-check-solid ms-3">
                        <input class="form-check-input" type="checkbox" value="1" id="confirm"
                               name="confirm" {{isset($marking_request)?'checked':''}}/>
                        <label class="form-check-label" for="confirm">
                            {{t('We pledge that all the standardized assessments of A.B.T for the aforementioned classes have been concluded, and can confirm that all students have completed their assessments. We the school also grant A.B.T assessments the permission to
    grade and correct the assessments starting today.')}}
                        </label>
                    </div>

                </div>


            </div>

            <!-- /.col-6 -->
            <div class="col-6 d-none" id="bar">

                <label for="Reading" id="statistic_label" style="font-weight: bold;"
                       class="form-label">{{t('Students Statistics')}} <span
                        class="text-success">{{t('Complete')}} : </span> <span
                        class="text-danger">{{t('Incomplete')}} : </span></label>
                <div class="progress" style="height:2rem" id="progress">
                    <div class="progress-bar bg-success" role="progressbar" aria-label="Example with label" style=""
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-danger" role="progressbar" aria-label="Example with label" style=""
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>

                </div>
                {{--                    <div class="progress" id="progress">--}}
                {{--                        <div class="progress-bar bg-success" role="progressbar"--}}
                {{--                             style="" aria-valuenow="30" aria-valuemin="0"--}}
                {{--                             aria-valuemax="100">--}}
                {{--                        </div>--}}
                {{--                        <div class="progress-bar bg-danger" role="progressbar"--}}
                {{--                             style="" aria-valuenow="20"--}}
                {{--                             aria-valuemin="0" aria-valuemax="100">--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
            </div>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mt-2">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <span class="text">{{isset($marking_request)?t('Update'):t('Create')}}</span>
                    </button>
                </div>
            </div>
        </form>

    </div>

@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\MarkingRequestRequest::class, '#form_information'); !!}

    <script>
        $(document).ready(function () {
            $("input:checkbox").change(function () {
                if ($(this).val() === "all_grades") {
                    $('input[name="grades[]"]').prop('checked', this.checked);
                } else if ($('.grade:checked').length != $('.grade').length && $(this).val() != "all_grades") {
                    $('input[name="all_grades"]').attr('checked', false);
                }
            });

            $('#year_id').on('change', function () {
                getStatistics()
            })
            $('#round').on('change', function () {
                getStatistics()
            })

            function getStatistics() {
                let data = {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                }

                let form_data = $('#form_information').serializeArray();
                $.each(form_data, function (key, val) {
                    data[val.name] = val.value;
                });
                // $('input:checkbox[name="grades"])
                data['grades'] = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                console.log('data', data)
                $('#bar').addClass('d-none');

                $.ajax({
                    url: "{{route('school.completed-terms-total')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        let response = data.data
                        $('#statistic_label .text-success').text('Complete : ' + response.student_terms_count);
                        $('#statistic_label .text-danger').text('Incomplete : ' + response.remind);

                        $('#progress .bg-success').text(parseFloat(response.value).toFixed(2) + '%');
                        $('#progress .bg-danger').text(parseFloat(response.remind_per).toFixed(2) + '%');

                        $('#progress .bg-success').css('width', parseFloat(response.value).toFixed(2) + '%');
                        $('#progress .bg-danger').css('width', parseFloat(response.remind_per).toFixed(2) + '%');
                        console.log(data.data);
                        $('#bar').removeClass('d-none');
                    }
                });
            }
        });
    </script>

@endsection
