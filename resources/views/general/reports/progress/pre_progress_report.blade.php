{{--Dev Omar Shaheen
    Devomar095@gmail.com
    WhatsApp +972592554320
    --}}
@extends('school.layout.container')
@section('title',$title)
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">{{$title}}</li>
@endpush
@section('content')
    <form id="form-report" action="{{route('school.report.progress')}}">
        <input type="hidden" name="combined" id="combined" value="0">
        <div class="row">
            <div class="col-12 mb-2">
                <label class="form-label">{{t('Year')}} :</label>
                <select name="year_id" id="year_id" class="form-select assessment-year" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 mb-2">
                <label class="form-label">{{t('Student Type')}} :</label>
                <div class="d-flex gap-2 p-4 border-secondary ms-1" style="border: 1px solid;border-radius: 5px;">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"  type="radio" name="student_type" value="1">
                        <label class="form-check-label" for="section">{{t('Arabs')}}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="student_type" value="0">
                        <label class="form-check-label" for="section">{{t('Non-arabs')}}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" checked type="radio" name="student_type" value="2">
                        <label class="form-check-label" for="section">{{t('Arabs & Non-Arabs')}}</label>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <label class="form-label">{{t('Grades')}} :</label>
                <div class="col-12 row p-4 border-secondary ms-1" style="border: 1px solid;border-radius: 5px;">
                    <div class="col-12 form-check form-check-custom form-check-solid form-check-sm mb-1">
                        <input class="form-check-input grade" type="checkbox" name="all_levels" checked value="all_levels">
                        <label class="form-check-label" for="all_grades">{{t('All')}}</label>
                    </div>
                    @for($i=1; $i<=12;$i++)
                        <div class="col-2 form-check form-check-custom form-check-solid form-check-sm mb-1">
                            <input class="form-check-input grade" checked type="checkbox" name="grades[]" value="{{$i}}">
                            <label class="form-check-label" for="student_section">{{t('Grade').$i}}</label>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button data-combined="0" type="button" data-route="{{route('school.report.progress')}}" class="btn btn-primary get-report">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <span class="text">{{t('Get Report')}}</span>
                    </button>
                    <button data-combined="1" type="button" data-route="{{route('school.report.progress')}}" class="btn btn-primary get-report ms-2">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <span class="text">{{t('Get Combined Report')}}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#form-report').submit(function (e) {
                e.preventDefault();
            });
            $('.get-report').click(function (e) {
                e.preventDefault();
                var route = $(this).data('route');
                $('#combined').val($(this).data('combined'));
                var formData = $("#form-report").serialize();
                window.open(route + '?' + formData, "_blank");
            });

            $(document).on('change', "input:checkbox", function () {
                if ($(this).val() === "all_levels") {
                    $('input[name="grades[]"]').prop('checked', this.checked);
                } else if ($('.term:checked').length != $('.term').length && $(this).val() != "all_curriculums") {
                    $('input[name="all_levels"]').attr('checked', false);
                }
            });
        });
    </script>
@endsection
