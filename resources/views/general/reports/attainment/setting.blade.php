@extends(getGuard().'.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{route(getGuard().'.report.attainment')}}"
          method="get">
        <div class="form-group row">
            <input type="hidden" name="summary" id="summary" value="0">
            <input type="hidden" name="combined" id="combined" value="0">
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Student Type')}} :</label>
                <select name="student_type" id="student_type" class="form-control form-select" data-control="select2"
                        data-placeholder="{{t('Student Type')}}">
                    <option value="0">{{t('All')}}</option>
                    <option value="1">{{t('Arabs')}}</option>
                    <option value="2">{{t('Non-Arabs')}}</option>
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Academic Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select Year')}}" id="year_id" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option
                                value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            @if(getGuard() == 'manager')
                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('School')}}:</label>
                    <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                            data-placeholder="{{t('Select School')}}" id="school_id" name="school_id">
                        <option></option>
                        @foreach($schools as $school)
                            <option
                                    value="{{$school->id}}">{{$school->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Sections')}} :</label>
                <select name="sections[]" multiple id="sections" class="form-control form-select" data-control="select2"
                        data-placeholder="{{t('Select Sections')}}" data-allow-clear="true">
                    <option></option>
                    @isset($sections)
                        @foreach($sections as $section)
                            <option value="{{$section}}">{{$section}}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-lg-12 mb-2">
                <label class="form-label mb-1">{{t('Grades')}} :</label>
                <div class="mb-10 p-4 border-secondary " style="border: 1px solid;border-radius: 5px;">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="form-check form-check-custom form-check-solid me-3">
                                <input class="form-check-input" checked type="checkbox" name="all_grades"
                                       value="all_grades" id="flexCheckbox30"/>
                                <label class="form-check-label" for="flexCheckbox30">
                                    {{t('All')}}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        @foreach(\App\Helpers\Constant::GRADES as $grade)
                            <div class="col-2 mb-2">
                                <div class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input grade" name="grades[]" checked type="checkbox"
                                           value="{{$grade}}" id="flexCheckbox{{$grade}}"/>
                                    <label class="form-check-label" for="flexCheckbox{{$grade}}">
                                        {{t('Grade')}} {{$grade}}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                                       checked name="include_sen"
                                />
                                <label class="form-check-label" for="flexSwitchDefault">
                                    {{t('Include SEN')}}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault"
                                       checked name="include_g_t"
                                />
                                <label class="form-check-label" for="flexSwitchDefault">
                                    {{t('Include G&T')}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button data-summary="0" data-combined="0"  type="button" class="get-report btn btn-primary me-5"
                        data-route="{{route(getGuard().'.report.attainment')}}">{{t('The Attainment')}}</button>
                <button data-summary="1" data-combined="0"  type="button" class="get-report btn btn-primary me-5"
                        data-route="{{route(getGuard().'.report.attainment')}}">{{t('The Attainment Summary')}}</button>
                <button data-summary="0" data-combined="1" type="button" class="get-report btn btn-primary "
                        data-route="{{route(getGuard().'.report.attainment')}}">{{t('The Combined Report')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script>
        $(document).ready(function () {
            //disable default submit form
            $('#form_data').submit(function (e) {
                e.preventDefault();
            });
            $('.get-report').click(function (e) {
                e.preventDefault();
                var route = $(this).data('route');
                $('#summary').val($(this).data('summary'));
                $('#combined').val($(this).data('combined'));
                var formData = $("#form_data").serialize();
                window.open(route + '?' + formData, "_blank");
                // form.submit();
            });
            $("input:checkbox").change(function () {
                if ($(this).val() === "all_grades") {
                    $('input[name="grades[]"]').prop('checked', this.checked);
                } else if ($('.grade:checked').length != $('.grade').length && $(this).val() != "all_grades") {
                    $('input[name="all_grades"]').prop('checked', false);
                } else if ($('.grade:checked').length == $('.grade').length && $(this).val() != "all_grades") {
                    $('input[name="all_grades"]').prop('checked', true);
                }
            });
        });


        getAndSetDataOnSelectChange('year_id', 'sections[]', '{{route(getGuard().'.get-sections')}}', 1, ['year_id']);
    </script>
    {!! JsValidator::formRequest(\App\Http\Requests\General\Report\AttainmentRequest::class, '#form_data'); !!}
@endsection
