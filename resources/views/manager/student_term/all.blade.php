@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{$title}}</li>
@endpush
@section('style')
    <style>
        #datatable tr.st-corrected > td:first-child {
            border-inline-start: 4px solid var(--bs-success) !important;
        }

        #datatable tr.st-uncorrected > td:first-child {
            border-inline-start: 4px solid var(--bs-warning) !important;
        }

        #status_tabs .nav-link {
            border: 1px dashed var(--bs-gray-300);
        }

        #status_tabs .nav-link.active {
            border-style: solid;
        }

        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .qf-item {
            flex: 0 1 190px;
            min-width: 150px;
        }

        .qf-id {
            flex: 0 1 150px;
            min-width: 120px;
        }

        .qf-text {
            flex: 0 1 200px;
            min-width: 150px;
        }

        #filters_drawer {
            width: 440px;
            max-width: 100vw;
            box-shadow: 0 0 30px rgba(0, 0, 0, .12);
        }

        #filters_drawer .drawer-group-title {
            font-size: .8rem;
            color: var(--bs-gray-500);
            border-bottom: 1px dashed var(--bs-gray-300);
            padding-bottom: .4rem;
            margin: 1.25rem 0 .75rem;
        }

        #filters_drawer .drawer-group-title:first-child {
            margin-top: 0;
        }

        #filters_drawer label {
            font-size: .85rem;
            color: var(--bs-gray-700);
            margin-bottom: .25rem;
        }

        /* The date picker sits at z-index 1000, under the drawer (1045) */
        .daterangepicker {
            z-index: 1060 !important;
        }

        #active_filters .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .6rem;
            border-radius: 2rem;
            background: var(--bs-light-primary);
            color: var(--bs-primary);
            font-size: .8rem;
        }

        #active_filters .chip-remove {
            cursor: pointer;
            opacity: .7;
        }
    </style>
@endsection

@section('actions')
    <a class="btn btn-primary" onclick="correcting()">{{t('Correcting')}} <i class="fa fa-share ms-2"></i></a>

    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @can('export students terms')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.student-term.export')}}')">{{t('Export')}}</a></li>
            @endcan
            <li class="only-corrected">
                <a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.reports.pdfCertificates')}}')">{{t('Export Certificates')}}</a>
            </li>
            @can('auto correct students terms')
                <li class="hide-on-deleted">
                    <a class="dropdown-item text-success" href="#!" data-filtered-action="{{route('manager.auto-correct-student-term')}}">{{t('Auto Correct')}}</a>
                </li>
            @endcan
            @can('delete students terms')
                <li class="hide-on-deleted">
                    <a class="dropdown-item text-danger" href="#!" data-filtered-action="{{route('manager.student-term.delete-duplicate-student-term')}}"
                       data-confirm="{{t('Are sure of the deleting process ?')}}">{{t('Delete Duplicate')}}</a>
                </li>
                <li class="hide-on-deleted">
                    <a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a>
                </li>
            @endcan
        </ul>
    </div>
@endsection

@section('content')
    <form id="filter" class="filter" autocomplete="off" onsubmit="return false;">
        {{-- Status tab: the existing `corrected` filter, 1 corrected / 2 uncorrected / empty both --}}
        <input type="hidden" name="corrected" id="status_type" value="{{$corrected}}"/>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-2" id="quick_filters">
            <div class="qf-id">
                <input type="text" name="student_id" class="form-control form-control-sm quick-text" inputmode="numeric"
                       value="{{request('student_id')}}" placeholder="{{t('Student Number')}}" title="{{t('Student Number')}}"/>
            </div>
            <div class="qf-id">
                <input type="text" name="id" class="form-control form-control-sm quick-text" inputmode="numeric"
                       value="{{request('id')}}" placeholder="{{t('Assessment Number')}}" title="{{t('Assessment Number')}}"/>
            </div>
            <div class="qf-item">
                <select class="form-select form-select-sm" data-control="select2" data-allow-clear="true" style="width:100%"
                        data-placeholder="{{t('Select School')}}" name="school_id" id="school_id">
                    <option></option>
                    @foreach($schools as $school)
                        <option value="{{$school->id}}" {{request('school_id') == $school->id ? 'selected' : ''}}>{{$school->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="qf-item">
                <select class="form-select form-select-sm" data-control="select2" data-allow-clear="true" style="width:100%"
                        data-placeholder="{{t('Select Year')}}" name="year_id" id="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{$year->id}}" {{request('year_id') == $year->id ? 'selected' : ''}}>{{$year->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="qf-item">
                <select class="form-select form-select-sm" data-control="select2" data-allow-clear="true" data-hide-search="true"
                        style="width:100%" data-placeholder="{{t('Round')}}" name="round">
                    <option></option>
                    @foreach(['september', 'february', 'may'] as $round)
                        <option value="{{$round}}" {{request('round') == $round ? 'selected' : ''}}>{{t($round)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="qf-item">
                <select class="form-select form-select-sm direct-value" data-control="select2" data-allow-clear="true" style="width:100%"
                        data-placeholder="{{t('Grade')}}" multiple name="grade[]">
                    @foreach(range(1, 12) as $grade)
                        <option value="{{$grade}}">{{$grade}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div class="qf-text">
                <input type="text" name="student_name" class="form-control form-control-sm quick-text" placeholder="{{t('Name')}}" title="{{t('Name')}}"/>
            </div>
            <div class="qf-text">
                <input type="text" name="email" class="form-control form-control-sm quick-text" placeholder="{{t('Email')}}" title="{{t('Email')}}"/>
            </div>
            <div class="qf-text">
                <input type="text" name="student_id_number" class="form-control form-control-sm quick-text" placeholder="{{t('SID')}}" title="{{t('SID')}}"/>
            </div>
            <button type="button" class="btn btn-sm btn-light-primary text-nowrap" data-bs-toggle="offcanvas" data-bs-target="#filters_drawer">
                <i class="fa fa-sliders-h me-1"></i>{{t('More Filters')}}
                <span class="badge badge-circle badge-primary ms-1 d-none" id="more_filters_count">0</span>
            </button>
            <button type="button" class="btn btn-sm btn-light text-nowrap" id="kt_reset"><i class="la la-close"></i>{{t('Reset')}}</button>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3" id="active_filters"></div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="filters_drawer" data-bs-scroll="true" data-bs-backdrop="false">
            <div class="offcanvas-header border-bottom py-4">
                <h4 class="offcanvas-title mb-0"><i class="fa fa-sliders-h me-2 text-primary"></i>{{t('More Filters')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body py-4">
                <div class="drawer-group-title">{{t('School')}} / {{t('Assessment')}}</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label>{{t('Level')}} <span class="text-muted fs-8">({{t('Year')}})</span></label>
                        <select class="form-select form-select-sm" data-control="select2" data-allow-clear="true" style="width:100%"
                                data-placeholder="{{t('Select Level')}}" name="level_id" id="levels_id" data-label="{{t('Level')}}">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label>{{t('Class Name')}} <span class="text-muted fs-8">({{t('School')}} + {{t('Year')}})</span></label>
                        <select id="class_name" name="grade_name[]" class="form-select form-select-sm direct-value" data-control="select2"
                                data-placeholder="{{t('Select Class Name')}}" data-allow-clear="true" multiple style="width:100%"
                                data-label="{{t('Class Name')}}">
                        </select>
                    </div>
                    <div class="col-12">
                        <label>{{t('Submission Date')}}</label>
                        <input class="form-control form-control-sm form-control-solid" name="submit_date" id="submit_date" autocomplete="off"
                               placeholder="{{t('Pick date rage')}}" data-label="{{t('Submission Date')}}" data-clear="#start_submit_date,#end_submit_date"/>
                        <input type="hidden" name="start_date" id="start_submit_date"/>
                        <input type="hidden" name="end_date" id="end_submit_date"/>
                    </div>
                    <div class="col-6">
                        <label>{{t('Student Duplicated')}}</label>
                        <select name="duplicated" class="form-select form-select-sm" data-control="select2" data-hide-search="true" style="width:100%"
                                data-placeholder="{{t('Select Student Type')}}" data-allow-clear="true" data-label="{{t('Student Duplicated')}}">
                            <option></option>
                            <option value="1">{{t('Duplicated')}}</option>
                        </select>
                    </div>
                </div>

                <div class="drawer-group-title">{{t('Display')}}</div>
                <div class="row g-3">
                    <div class="col-6">
                        <label>{{t('Order By')}}</label>
                        <select name="orderBy" class="form-select form-select-sm reset-no" data-control="select2" data-hide-search="true"
                                style="width:100%" data-label="{{t('Order By')}}" data-default="latest">
                            <option value="latest" selected>{{t('Latest')}}</option>
                            <option value="name">{{t('Name')}}</option>
                            <option value="level">{{t('Level')}}</option>
                            <option value="section">{{t('Section')}}</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>{{t('Students Terms Status')}}</label>
                        <select class="form-select form-select-sm reset-no" data-control="select2" data-hide-search="true" style="width:100%"
                                name="deleted_at" id="terms_status" data-label="{{t('Students Terms Status')}}" data-default="1">
                            <option value="1" selected>{{t('Not Deleted Terms')}}</option>
                            @can('show deleted students terms')
                                <option value="2">{{t('Deleted Terms')}}</option>
                            @endcan
                        </select>
                    </div>
                </div>
            </div>
            <div class="border-top p-4 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-light" id="drawer_clear"><i class="la la-close"></i>{{t('Reset')}}</button>
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="offcanvas">{{t('Close')}}</button>
            </div>
        </div>
    </form>

    @include('manager.student_term.partials.all_tabs')

    <table class="table table-row-bordered table-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('School')}}</th>
            <th class="text-start">{{t('Class Name')}}</th>
            <th class="text-start">{{t('Assessment')}}</th>
            <th class="text-start">{{t('Status')}} / {{t('Total')}}</th>
            <th class="text-start">{{t('Submitted At')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

@section('script')
    <script>
        @can('delete students terms')
        var DELETE_URL = "{{route('manager.student.delete-student-term')}}";
        @endcan
        var TABLE_URL = "{{route('manager.student_term.all')}}";
        var TABLE_COLUMNS = [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'school', name: 'school'},
            {data: 'grade_name', name: 'grade_name'},
            {data: 'term_data', name: 'term_data'},
            {data: 'result', name: 'result'},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions'}
        ];
        var CREATED_ROW = function (row, data) {
            if (data.sen) {
                $(row).css('background-color', '#ffacac');
            }
            if (data.g_t) {
                $(row).css('background-color', '#fff0b0');
            }
        };

        function restore(id) {
            $.ajax({
                type: "POST",
                url: '{{route('manager.student-term-restore', ':id')}}'.replace(':id', id),
                data: {'_token': '{{csrf_token()}}'},
                success: function (result) {
                    toastr.success(result.message);
                    table.DataTable().draw(false);
                },
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                }
            });
        }

        //open many correcting pages in blank
        function correcting() {
            let ids = getCheckedRows();
            let route = "{{route('manager.student_term.edit', ':id')}}";
            if (ids.length === 0) {
                $("input:checkbox[name='rows[]']").each(function () {
                    ids.push($(this).val());
                });
            }
            for (let i = 0; i < ids.length; i++) {
                let page = window.open(route.replace(':id', ids[i]), '_blank');
                if (!page || page.closed || typeof page.closed == 'undefined') {
                    alert('{{t('Pop-up is blocked! Please allow pop-ups and redirects in your browser settings to open all students assessments pages.')}}');
                    return false;
                }
            }
        }

        // Classes of the chosen school and year
        function loadSections() {
            let year_id = $('#year_id').val();
            if (!year_id) {
                $('#class_name').empty();
                return;
            }
            $.ajax({
                url: '{{route('manager.student.get-sections')}}',
                data: {id: year_id, school_id: $('#school_id').val()},
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#class_name').empty();
                    $.each(data, function (key, value) {
                        $('#class_name').append(value);
                    });
                }
            });
        }
        $(document).on('change', '#year_id, #school_id', loadSections);
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v1"></script>
    <script>
        initializeDateRangePicker('submit_date');
    </script>
    @include('manager.student_term.partials.all_script')
@endsection
