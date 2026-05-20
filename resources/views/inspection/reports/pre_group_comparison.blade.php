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
          action="{{route('inspection.report.group_comparison_report')}}"
          method="get">
        <div class="form-group row">
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Schools')}}:</label>
                <select class="form-control form-select" id="schools" multiple data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Schools')}}" name="school_id[]">
                    <option value="all">{{t('All')}}</option>
                    @foreach($schools as $school)
                        <option
                            value="{{$school->id}}">{{$school->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label class="form-label mb-1">{{t('Year')}}:</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Year')}}" name="year_id">
                    <option></option>
                    @foreach(\App\Models\Year::query()->get() as $year)
                        <option
                            value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label  class="form-label mb-1">{{t('Student Type')}} :</label>
                <select name="student_type" id="student_type" class="form-control form-select" data-control="select2" data-placeholder="{{t('Student Type')}}" >
                    <option></option>
                    <option value="1">{{t('Arabs')}}</option>
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label  class="form-label mb-1">{{t('Sub Title')}} :</label>
                <input type="text" name="sub_title" class="form-control datatable-input"
                       placeholder="{{t('Sub Title')}}" data-col-index="1"/>
            </div>
            <div class="col-lg-4 mb-5">
                <label class="form-label mb-1">{{t('Round')}} :</label>
                <select class="form-control form-select" data-control="select2" data-allow-clear="true"
                        data-placeholder="{{t('Select One')}}" name="round">
                    <option></option>
                    <option value="september">{{t('September')}}</option>
                    <option value="february">{{t('February')}}</option>
                    <option value="may">{{t('May')}}</option>
                </select>
            </div>
            <div class="col-lg-12 mb-2">
                <label  class="form-label mb-1">{{t('Grades')}} :</label>
                    <div class="mb-1 row ps-3">
                        <div class="col-2 form-check form-check-custom form-check-solid">
                        <input class="form-check-input" checked type="checkbox" name="all_grades" value="all_grades" id="flexCheckbox30"/>
                        <label class="form-check-label" for="flexCheckbox30">
                            {{t('All')}}
                        </label>
                    </div>
                    </div>
                   <div class="mb-10 row ps-3">
                        @foreach($grades as $grade)
                        <div class="col-2 form-check form-check-custom form-check-solid mt-2">
                            <input class="form-check-input grade" name="grades[]" checked type="checkbox" value="{{$grade}}" id="flexCheckbox30"/>
                            <label class="form-check-label" for="flexCheckbox30">
                                {{t('Grade')}} {{$grade}}
                            </label>
                        </div>
                    @endforeach
                   </div>
            </div>


        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="get-report btn btn-primary me-5" data-route="{{route('inspection.report.group_comparison_report')}}">{{t('Get Report')}}</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script>
        $(document).ready(function (){
            $('.get-report').click(function (e){
                e.preventDefault();
                var route = $(this).data('route');
                var form = $('#form_data').attr('action',route);
                form.submit();
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

            //select or unselect options
            function onSelectAllClick(selectId) {
                let select = $('#' + selectId);
                //select all when all is clicked
                $(select).on("select2:clearing", function (e) {
                    e.preventDefault();
                })
                $(select).on("select2:select select2:unselect", function (e) {
                    //e.preventDefault()
                    let data = e.params.data;
                    console.log(e.params)
                    let options = $(select).find('option');
                    if (data.id === 'all') {
                        if (data.selected) { //select
                            options.each(function () {
                                $(this).prop('selected', true)
                            })
                            $(select).trigger('change');
                        } else { //unselect
                            $(select).val([])
                            $(select).trigger('change');
                        }
                    }

                });

            }

            function clearSelection(selectId) {
                let select = $('#' + selectId);
                $(select).val([])
                $(select).trigger('change');
            }

            onSelectAllClick('schools')
        });
    </script>
    {!! JsValidator::formRequest(\App\Http\Requests\Inspection\GroupComparingReportRequest::class, '#form_data'); !!}
@endsection
