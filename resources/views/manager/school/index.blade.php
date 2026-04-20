@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item b text-muted">
        {{$title}}
    </li>
@endpush

@section('actions')
    @can('add schools')
        <a href="{{route('manager.school.create')}}" class="btn btn-primary font-weight-bolder">
            <i class="la la-plus"></i>{{t('Create School')}}</a>
    @endcan
    <div class="dropdown" id="actions_dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
            {{t('Actions')}}
        </button>
        <ul class="dropdown-menu">
            @can('export schools')
                <li><a class="dropdown-item" href="#!" onclick="excelExport('{{route('manager.export-schools')}}')">{{t('Export')}}</a></li>
            @endcan
            @can('schools general scheduling')
                    <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#update_general_scheduling">{{t('General Scheduling')}}</a></li>
            @endcan
            @can('edit schools')
                <li><a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#update_proctoring_settings">{{t('Proctoring Settings')}}</a></li>
            @endcan
            @can('delete schools')
            <li><a class="dropdown-item text-danger d-none checked-visible" href="#!" id="delete_rows">{{t('Delete')}}</a></li>
            @endcan
        </ul>
    </div>

@endsection

@section('filter')
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('ID')}}:</label>
        <input type="text" name="id" class="form-control direct-search" placeholder="E.g: 4590"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Name')}}:</label>
        <input type="text" name="name" class="form-control direct-search" placeholder="{{t('Name')}}"
               data-col-index="0"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Email')}}:</label>
        <input type="text" name="email" class="form-control datatable-input"
               placeholder="{{t('Email')}}" data-col-index="1"/>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Country')}}:</label>
        <select class="form-control form-select"  data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Country')}}" name="country">
            <option></option>
            @foreach(schoolsCountry() as $key => $type)
                <option value="{{$key}}">{{$type}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Curriculum Type')}}:</label>
        <select class="form-control form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Curriculum Type')}}"
                name="curriculum_type">
            <option></option>
            @foreach(schoolsType() as $key => $type)
                <option value="{{$key}}">{{$type}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <label class="mb-1">{{t('Status')}}:</label>
        <select name="active" class="form-select" data-control="select2" data-placeholder="{{t('Select Status')}}" data-hide-search="true" data-allow-clear="true">
            <option></option>
            <option value="1">{{t('Active')}}</option>
            <option value="2">{{t('Inactive')}}</option>
        </select>
    </div>
@endsection
@section('content')
    <table class="table table-row-bordered gy-5" id="datatable">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th class="text-start"></th>
            <th class="text-start">{{t('Name')}}</th>
            <th class="text-start">{{t('Country')}}</th>
            <th class="text-start">{{t('Curriculum Type')}}</th>
            <th class="text-start">{{t('Logo')}}</th>
            <th class="text-start">{{t('Active Status')}}</th>
            <th class="text-start">{{t('Last Login')}}</th>
            <th class="text-start">{{t('Actions')}}</th>
        </tr>
        </thead>
    </table>

    @can('edit schools')
    <div class="modal fade" tabindex="-1" id="update_proctoring_settings">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{t('Bulk Update Proctoring Settings')}}</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form class="form-horizontal" id="proctoring_settings_form" action="{{route('manager.school.proctoring-settings.bulk-update')}}" method="post">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="alert alert-info p-3 mb-4" role="alert">
                            {{t('If you select schools from the table, the settings will be applied to the selected schools only. Otherwise, the settings will be applied to all schools matching the selected countries below. If no country is selected and no schools are checked, all schools will be updated.')}}
                        </div>
                        <div class="form-group row mb-3">
                            <label class="control-label col-md-4">{{t('Countries')}}</label>
                            <div class="col-md-8">
                                <select class="form-select" data-control="select2" data-allow-clear="true" data-placeholder="{{t('Select Countries')}}" name="countries[]">
                                    <option></option>
                                    @foreach(schoolsCountry() as $key => $type)
                                        <option value="{{$key}}">{{$type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="control-label col-md-4">{{t('Desktop Only')}}</label>
                            <div class="col-md-8">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="bulk_proctoring_desktop_only" name="proctoring_settings[desktop_only]"/>
                                    <label class="form-check-label" for="bulk_proctoring_desktop_only">{{t('Enable Desktop Only')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="control-label col-md-4">{{t('Screenshot')}}</label>
                            <div class="col-md-8">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="bulk_proctoring_screenshot" name="proctoring_settings[screenshot]"/>
                                    <label class="form-check-label" for="bulk_proctoring_screenshot">{{t('Enable Screenshot')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="control-label col-md-4">{{t('Selfie')}}</label>
                            <div class="col-md-8">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="bulk_proctoring_selfie" name="proctoring_settings[selfie]"/>
                                    <label class="form-check-label" for="bulk_proctoring_selfie">{{t('Enable Selfie')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{t('Cancel')}}</button>
                        <button type="button" class="btn btn-warning" id="proctoring_settings_confirm">{{t('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('schools general scheduling')
        <div class="modal fade" id="update_general_scheduling" tabindex="-1" role="dialog" aria-labelledby="updateModel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{t('General Scheduling')}}</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="form-horizontal" id="update_dorm"
                          action="{{route('manager.school.general-scheduling')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="control-label col-md-3">{{t('Round')}}</label>
                                <div class="col-md-6 mb-2">
                                    <select class="form-control form-select" required id="round" name="round" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="{{t('Select Round')}}">
                                        <option selected value="">{{t('Select Round')}}</option>
                                        <option value="september">{{t('September')}}</option>
                                        <option value="february">{{t('February')}}</option>
                                        <option value="may">{{t('May')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-3">{{t('Status')}}</label>
                                <div class="col-md-6">
                                    <select class="form-control form-select" required id="status" name="status" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="{{t('Select Status')}}">
                                        <option></option>
                                        <option value="1">{{t('Active')}}</option>
                                        <option value="2">{{t('Not-Active')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{t('Close')}}</button>
                            <button type="submit" class="btn btn-primary">{{t('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection
@section('script')
    <script>
        var DELETE_URL = "{{route('manager.delete-school')}}";
        var TABLE_URL = "{{route('manager.school.index')}}";
        var EXPORT_URL = '{{route('manager.export-schools')}}';
        var COLUMN_DEFS =  [
            {
                targets: 1,
                render: function (data, type, full, meta) {

                    return '<div class="student-box" style="text-align: start">\n' +
                        '                                    <div class="content">\n' +
                        '                                        <div class="student-name">' + full.name + '</div>\n' +
                        '                                        <div class="student-username">' + full.email + '</div>\n' +
                        '                                    </div>\n' +
                        '                                </div>';
                },
            },]
        var TABLE_COLUMNS = [
            {data: 'id', name: 'name'},
            {data: 'name', name: 'name'},
            {data: 'country', name: 'country'},
            {data: 'curriculum_type', name: 'curriculum_type'},
            {data: 'logo', name: 'logo'},
            {data: 'active', name: 'active'},
            {data: 'last_login', name: 'last_login'},
            {data: 'actions', name: 'actions'}
        ];
    </script>
    <script src="{{asset('assets_v1/js/datatable.js')}}?v={{time()}}"></script>
    <script>
            $('#update_proctoring_settings').on('show.bs.modal', function () {
                var form = $('#proctoring_settings_form');
                form.find('input[name="row_id[]"]').remove();
                $("input:checkbox[name='rows[]']:checked").each(function () {
                    form.append('<input type="hidden" name="row_id[]" value="' + $(this).val() + '"/>');
                });
            });
            function clearProctoringModal() {
                var form = $('#proctoring_settings_form');
                form.find('input[name="row_id[]"]').remove();
                form.find('select[name="countries[]"]').val(null).trigger('change');
                form.find('input[type="checkbox"]').prop('checked', false);
            }
            $('#proctoring_settings_confirm').click(function () {
                var form = $('#proctoring_settings_form');
                var formData = form.serialize();
                var rowIds = [];
                form.find('input[name="row_id[]"]').each(function () { rowIds.push($(this).val()); });
                var countriesText = form.find('select[name="countries[]"]').find('option:selected').map(function () { return $.trim($(this).text()); }).get().filter(Boolean);
                var desktopOnly = form.find('#bulk_proctoring_desktop_only').is(':checked');
                var screenshot = form.find('#bulk_proctoring_screenshot').is(':checked');
                var selfie = form.find('#bulk_proctoring_selfie').is(':checked');
                var scopeText = '';
                if (rowIds.length > 0) { scopeText = rowIds.length + ' {{t("schools selected from table")}}'; }
                else if (countriesText.length > 0) { scopeText = countriesText.join(' , '); }
                else { scopeText = '{{t("All schools")}}'; }
                var enabledList = [], disabledList = [];
                if (desktopOnly) enabledList.push("{{t('Desktop Only')}}"); else disabledList.push("{{t('Desktop Only')}}");
                if (screenshot) enabledList.push("{{t('Screenshot')}}"); else disabledList.push("{{t('Screenshot')}}");
                if (selfie) enabledList.push("{{t('Selfie')}}"); else disabledList.push("{{t('Selfie')}}");
                var detailsHtml = '<div style="text-align:start; font-size:14px;"><div class="mb-2"><strong>{{t("Apply to")}}:</strong> ' + scopeText + '</div>';
                if (enabledList.length > 0) { detailsHtml += '<div class="mb-1"><span class="text-success">&#10003;</span> <strong>{{t("Enable")}}:</strong> ' + enabledList.join(' , ') + '</div>'; }
                if (disabledList.length > 0) { detailsHtml += '<div><span class="text-danger">&#10005;</span> <strong>{{t("Disable")}}:</strong> ' + disabledList.join(' , ') + '</div>'; }
                detailsHtml += '</div>';
                $('#update_proctoring_settings').modal('hide');
                clearProctoringModal();
                Swal.fire({
                    title: "{{t('Are you sure?')}}",
                    html: detailsHtml, icon: 'warning',
                    showCancelButton: true, confirmButtonText: CONFIRM_TEXT, cancelButtonText: CANCEL_TEXT,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        showLoadingModal();
                        $.ajax({
                            type: 'POST', url: "{{route('manager.school.proctoring-settings.bulk-update')}}", data: formData,
                        }).done(function (data) {
                            hideLoadingModal();
                            if (data.success) { $('.group-checkable').prop('checked', false); table.DataTable().draw(false); Swal.fire("", data.message, "success"); }
                            else { Swal.fire("", data.message, "error"); }
                        }).fail(function (error) {
                            hideLoadingModal();
                            Swal.fire("", error.responseJSON ? error.responseJSON.message : "{{t('An error occurred')}}", "error");
                        });
                    }
                });
            });
    </script>

@endsection
