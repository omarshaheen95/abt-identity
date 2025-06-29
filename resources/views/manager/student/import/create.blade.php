@extends('manager.layout.container')
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.students_files_import.index')}}" class="text-muted">
            {{t('Student Import Files')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('style')
    <link href="{{asset('assets_v1/css/import-students.css')}}" rel="stylesheet" type="text/css"/>


@endsection

@section('special_content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Header -->
                <div class="import-header bg-primary">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-upload fs-2x me-3 text-white"></i>
                        <div>
                            <h3 class="mb-0 text-white">{{t('Import Students')}}</h3>
                            <p class="mb-0">{{t('Upload and manage student data efficiently')}}</p>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="import-body">
                    <form id="upload_students_file" method="POST" action="{{route('manager.students_files_import.store')}}" enctype="multipart/form-data">
                        @csrf

                        <!-- Main Import Configuration -->
                        <div class="main-section">
                            <div class="section-title">
                                <i class="fas fs-2x fa-cog text-primary"></i>
                                {{t('Import Configuration')}}
                            </div>

                            <div class="row">
                                <!-- School Selection -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{t('Select School')}} <span class="required-text">*</span>
                                    </label>
                                    <select name="school_id" class="form-select" required  data-control="select2"
                                            data-allow-clear="true" data-placeholder="{{t('Select School')}}">
                                        <option value="" disabled selected>{{t('Choose a school...')}}</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('school_id')
                                    <div class="text-danger mt-2">
                                        <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- Academic Year -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{t('Academic Year')}} <span class="required-text">*</span>
                                    </label>
                                    <select name="year_id" class="form-select" required>
                                        <option value="" disabled selected>{{t('Choose academic year...')}}</option>
                                        @foreach($years as $year)
                                            <option value="{{$year->id}}" {{ old('year_id') == $year->id ? 'selected' : '' }}>
                                                {{$year->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year_id')
                                    <div class="text-danger mt-2">
                                        <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- File Upload -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        {{t('Student File')}} <span class="required-text">*</span>
                                    </label>
                                    <div class="file-input-container">
                                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle"></i> {{t('Supported formats: Excel (.xlsx, .xls), CSV (.csv)')}}
                                        </small>
                                    </div>
                                    @error('file')
                                    <div class="text-danger mt-2">
                                        <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- Username Type -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{t('Username Type')}} <span class="required-text">*</span>
                                    </label>
                                    <select name="username_type" class="form-select" required>
                                        <option value="student_name" {{ old('username_type', 'student_name') == 'student_name' ? 'selected' : '' }}>
                                            {{ t('Student Name') }}
                                        </option>
                                        <option value="student_id" {{ old('username_type') == 'student_id' ? 'selected' : '' }}>
                                            {{ t('Student ID') }}
                                        </option>
                                    </select>
                                    @error('username_type')
                                    <div class="text-danger mt-2">
                                        <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Process Type -->
                            <div class="mt-4">
                                <label class="form-label mb-3">
                                    {{t('Select Process Type')}} <span class="required-text">*</span>
                                </label>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="process-option" data-process="create">
                                            <input class="form-check-input me-2" type="radio" value="create" name="process_type"
                                                   id="process_create" {{ old('process_type', 'create') == 'create' ? 'checked' : '' }} required>
                                            <label class="form-check-label w-100" for="process_create">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fs-2x fa-plus-circle text-success fa-lg me-2"></i>
                                                    <div>
                                                        <div class="option-text">{{t('Create Students')}}</div>
                                                        <div class="option-description">{{t('Add new student records')}}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="process-option" data-process="update">
                                            <input class="form-check-input me-2" type="radio" value="update" name="process_type"
                                                   id="process_update" {{ old('process_type') == 'update' ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="process_update">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fs-2x fa-edit text-primary fa-lg me-2"></i>
                                                    <div>
                                                        <div class="option-text">{{t('Update Students')}}</div>
                                                        <div class="option-description">{{t('Modify existing records')}}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="process-option" data-process="delete">
                                            <input class="form-check-input me-2" type="radio" value="delete" name="process_type"
                                                   id="process_delete" {{ old('process_type') == 'delete' ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="process_delete">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fs-2x fa-trash-alt text-danger fa-lg me-2"></i>
                                                    <div>
                                                        <div class="option-text">{{t('Delete Students')}}</div>
                                                        <div class="option-description">{{t('Remove student data')}}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('process_type')
                                <div class="text-danger mt-2">
                                    <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden Status Field -->
                        <input type="hidden" name="status" id="status_field" value="{{ old('status', 'create') }}">

                        <!-- Create Options -->
                        <div class="section-card" id="with_abt_id_section">
                            <div class="section-title">
                                <i class="fas fs-2x fa-id-badge text-primary"></i>
                                {{t('Student ID Options')}}
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    {{t('Generate ABT ID for new students')}} <span class="required-text">*</span>
                                </label>
                                <div class="checkbox-description mb-3">{{t('Automatically assign unique ABT identifiers')}}</div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="checkbox-container">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1" name="with_abt_id"
                                                       id="with_abt_id_yes" {{ old('with_abt_id') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="with_abt_id_yes">
                                                    <i class="fas fs-2x fa-check-circle text-success me-1"></i> {{t('Yes')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox-container">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0" name="with_abt_id"
                                                       id="with_abt_id_no" {{ old('with_abt_id', '0') == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="with_abt_id_no">
                                                    <i class="fas fs-2x fa-times-circle text-danger me-1"></i> {{t('No')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('with_abt_id')
                            <div class="text-danger mt-2">
                                <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Update Options -->
                        <div class="section-card d-none" id="search_by_section">
                            <div class="section-title">
                                <i class="fas fs-2x fa-search text-primary"></i>
                                {{t('Search Criteria')}}
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">
                                        {{t('Search Records By')}} <span class="required-text">*</span>
                                    </label>
                                    <div class="checkbox-description mb-3">{{t('Choose the field to match existing records')}}</div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="checkbox-container">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="student_id" name="search_by_column"
                                                           id="update_by_student_id" {{ old('search_by_column') == 'student_id' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="update_by_student_id">
                                                        <i class="fas fs-1x fa-id-card text-primary me-1"></i> {{t('Student ID')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="checkbox-container">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="username" name="search_by_column"
                                                           id="update_by_username" {{ old('search_by_column') == 'username' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="update_by_username">
                                                        <i class="fas fs-1x fa-user text-info me-1"></i> {{t('Username')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('search_by_column')
                                    <div class="text-danger mt-2">
                                        <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Delete Options -->
                        <div class="section-card danger d-none" id="delete_options_section">
                            <div class="section-title">
                                <i class="fas fs-2x fa-exclamation-triangle text-danger"></i>
                                {{t('Deletion Options')}}
                            </div>

                            <div class="warning-alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>{{t('Warning!')}} </strong>{{t('This action cannot be undone. Please proceed with caution.')}}
                            </div>

                            <label class="form-label mb-3">
                                {{t('Delete Type')}} <span class="required-text">*</span>
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="process-option">
                                        <input class="form-check-input me-2" type="radio" value="delete_assessments" name="delete_type"
                                               id="delete_assessments" {{ old('delete_type') == 'delete_assessments' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="delete_assessments">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fs-2x fa-clipboard-list text-warning fa-lg me-2"></i>
                                                <div>
                                                    <div class="option-text">{{t('Delete Assessments Only')}}</div>
                                                    <div class="option-description">{{t('Remove assessment data while keeping students')}}</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="process-option">
                                        <input class="form-check-input me-2" type="radio" value="delete_all" name="delete_type"
                                               id="delete_all" {{ old('delete_type') == 'delete_all' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="delete_all">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fs-2x fa-user-times text-danger fa-lg me-2"></i>
                                                <div>
                                                    <div class="option-text">{{t('Delete All Student Data')}}</div>
                                                    <div class="option-description">{{t('Permanently remove all student records')}}</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('delete_type')
                            <div class="text-danger mt-2">
                                <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                            </div>
                            @enderror

                            <!-- Assessment Year -->
{{--                            <div id="assessment_year_section" class="d-none mt-3">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-md-6">--}}
{{--                                        <label class="form-label">--}}
{{--                                            {{t('Assessment Year')}} <span class="required-text">*</span>--}}
{{--                                        </label>--}}
{{--                                        <select name="year_deleted_assessments" class="form-select">--}}
{{--                                            <option value="">{{t('Select assessment year...')}}</option>--}}
{{--                                            @foreach($years as $year)--}}
{{--                                                <option value="{{$year['name']}}" {{ old('year_deleted_assessments') == $year['name'] ? 'selected' : '' }}>--}}
{{--                                                    {{$year['name']}}--}}
{{--                                                </option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                        @error('year_deleted_assessments')--}}
{{--                                        <div class="text-danger mt-2">--}}
{{--                                            <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}--}}
{{--                                        </div>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <!-- Assessment Rounds -->
                            <div id="assessment_rounds_section" class="d-none mt-3">
                                <label class="form-label">
                                    {{t('Assessment Rounds')}} <span class="required-text">*</span>
                                </label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="checkbox-container">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="september" name="rounds_deleted_assessments[]"
                                                       id="round_september" {{ in_array('September', old('rounds_deleted_assessments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="round_september">
                                                    <i class="fas fa-calendar-day text-primary"></i> {{t('September Round')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="checkbox-container">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="february" name="rounds_deleted_assessments[]"
                                                       id="round_february" {{ in_array('February', old('rounds_deleted_assessments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="round_february">
                                                    <i class="fas fa-calendar-day text-info"></i> {{t('February Round')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="checkbox-container">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="may" name="rounds_deleted_assessments[]"
                                                       id="round_may" {{ in_array('May', old('rounds_deleted_assessments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="round_may">
                                                    <i class="fas fa-calendar-day text-success"></i> {{t('May Round')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('rounds_deleted_assessments')
                                <div class="text-danger mt-2">
                                    <i class="fas fs-2x fa-exclamation-circle"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Information and Download -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-3"></i>
                                        <div>
                                            <h6 class="mb-1 text-dark">{{t('Important Note')}}</h6>
                                            <p class="mb-0 text-dark">{{$note}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="download-box">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{t('Need a template?')}}</h6>
                                            <p class="mb-0 text-dark">{{t('Download our example file format')}}</p>
                                        </div>
                                        <a href="{{asset('Students Example Sheet.xlsx')}}" class="btn btn-success btn-modern btn-sm">
                                            <i class="fas fa-download"></i> {{t('Download Example')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <a href="{{route('manager.students_files_import.index')}}" class="btn btn-light btn-modern">
                                <i class="fas fs-2x fa-arrow-left"></i> {{t('Cancel')}}
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fs-2x fa-upload"></i> {{t('Start Import Process')}}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ImportStudentFileRequest::class, '#upload_students_file'); !!}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Process type handling (large cards)
            const processOptions = document.querySelectorAll('.process-option');
            const processRadios = document.querySelectorAll('input[name="process_type"]');

            processOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const processType = this.dataset.process;
                    if (processType) {
                        const radio = document.getElementById(`process_${processType}`);
                        if (radio) {
                            radio.checked = true;
                            radio.dispatchEvent(new Event('change'));
                        }
                    } else {
                        // Handle other radio options (delete_type)
                        const radioInput = this.querySelector('input[type="radio"]');
                        if (radioInput) {
                            radioInput.checked = true;
                            radioInput.dispatchEvent(new Event('change'));
                        }
                    }

                    // Remove selected class from siblings with same name
                    const radioInput = this.querySelector('input[type="radio"]');
                    if (radioInput) {
                        const radioName = radioInput.name;
                        document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
                            radio.closest('.process-option')?.classList.remove('selected');
                        });
                    }

                    this.classList.add('selected');
                });
            });

            // Handle small radio buttons (ABT ID and Update By)
            const smallRadioContainers = document.querySelectorAll('.checkbox-container');
            smallRadioContainers.forEach(container => {
                container.addEventListener('click', function() {
                    const radioInput = this.querySelector('input[type="radio"]');
                    if (radioInput) {
                        radioInput.checked = true;

                        // Remove selected class from siblings
                        const radioName = radioInput.name;
                        document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
                            radio.closest('.checkbox-container')?.classList.remove('selected');
                        });

                        this.classList.add('selected');
                    }
                });
            });

            processRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const processType = this.value;

                    // Hide all conditional sections
                    document.getElementById('with_abt_id_section').classList.add('d-none');
                    document.getElementById('search_by_section').classList.add('d-none');
                    document.getElementById('delete_options_section').classList.add('d-none');

                    // Show relevant section
                    if (processType === 'create') {
                        document.getElementById('with_abt_id_section').classList.remove('d-none');
                    } else if (processType === 'update') {
                        document.getElementById('search_by_section').classList.remove('d-none');
                    } else if (processType === 'delete') {
                        document.getElementById('search_by_section').classList.remove('d-none');
                        document.getElementById('delete_options_section').classList.remove('d-none');
                    }

                    // Update hidden field
                    document.getElementById('status_field').value = processType;
                });
            });

            // Delete type handling
            const deleteRadios = document.querySelectorAll('input[name="delete_type"]');
            deleteRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const deleteType = this.value;
                    // const yearSection = document.getElementById('assessment_year_section');
                    const roundsSection = document.getElementById('assessment_rounds_section');

                    if (deleteType === 'delete_assessments') {
                        // yearSection.classList.remove('d-none');
                        roundsSection.classList.remove('d-none');
                    } else {
                        // yearSection.classList.add('d-none');
                        roundsSection.classList.add('d-none');
                    }
                });
            });

            // Form submission
            document.getElementById('upload_students_file').addEventListener('submit', function(e) {
                const processType = document.querySelector('input[name="process_type"]:checked').value;

                if (processType === 'delete') {
                    const deleteType = document.querySelector('input[name="delete_type"]:checked');
                    if (deleteType && deleteType.value === 'delete_assessments') {
                        const selectedRounds = document.querySelectorAll('input[name="rounds_deleted_assessments[]"]:checked');
                        if (selectedRounds.length === 0) {
                            e.preventDefault();
                            alert('{{t("Please select at least one assessment round to delete")}}');
                            return false;
                        }
                    }
                }

                if (!$('#upload_students_file').validate().form()) {
                    // If the form is invalid, show an error message
                    Swal.fire({
                        icon: 'error',
                        title: '{{t('Error')}}',
                        text: '{{t('Please fill in all required fields correctly.')}}'
                    });
                    return;
                }
                // Show loading
                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fs-2x fa-spinner fa-spin"></i> {{t("Processing...")}}';
                submitBtn.disabled = true;
            });

            // Initialize based on old values
            const currentProcess = document.querySelector('input[name="process_type"]:checked');
            if (currentProcess) {
                currentProcess.dispatchEvent(new Event('change'));

                const processOption = document.querySelector(`[data-process="${currentProcess.value}"]`);
                if (processOption) {
                    processOption.classList.add('selected');
                }
            }

            const currentDeleteType = document.querySelector('input[name="delete_type"]:checked');
            if (currentDeleteType) {
                currentDeleteType.dispatchEvent(new Event('change'));
                const deleteOption = currentDeleteType.closest('.process-option');
                if (deleteOption) {
                    deleteOption.classList.add('selected');
                }
            }

            // Initialize ABT ID selection
            const currentAbtId = document.querySelector('input[name="with_abt_id"]:checked');
            if (currentAbtId) {
                const abtContainer = currentAbtId.closest('.checkbox-container');
                if (abtContainer) {
                    abtContainer.classList.add('selected');
                }
            }

            // Initialize update by column selection
            const currentUpdateBy = document.querySelector('input[name="search_by_column"]:checked');
            if (currentUpdateBy) {
                const updateContainer = currentUpdateBy.closest('.checkbox-container');
                if (updateContainer) {
                    updateContainer.classList.add('selected');
                }
            }
        });
    </script>

@endsection
