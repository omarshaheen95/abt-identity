@extends('manager.layout.container')
@section('pre_style')
    <style>
        .no-errors-container {
            background: linear-gradient(135deg, #f8fff8 0%, #f0fdf4 100%);
            border-radius: 0.625rem;
            border: 1px solid #bbf7d0;
        }

        .error-item {
            border: 1px solid #e3e6f0;
            border-radius: 0.475rem;
            transition: all 0.2s ease;
        }

        .error-item:hover {
            border-color: #f1416c;
            box-shadow: 0 2px 8px rgba(241, 65, 108, 0.1);
        }

        .error-header {
            background: #fff;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: none;
        }

        .error-header:not(.collapsed) {
            background: #fef2f2;
            border-bottom: 1px solid #f87171;
        }

        .error-header:focus {
            box-shadow: none;
            border-color: #f1416c;
        }

        .alert-sm {
            font-size: 0.875rem;
        }

        .input-data-compact {
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 0.375rem;
            padding: 0.75rem;
        }

        .badge-danger {
            background-color: #f1416c;
            color: white;
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .accordion-button::after {
            background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23f1416c'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
        }

        .border-bottom.border-light:last-child {
            border-bottom: none !important;
        }

        .error-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
@endsection
@section('page-title')
    {{$title}}
@endsection
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
@section('content')
    @if($file->logErrors->count() > 0)
        <!-- Summary Statistics -->
        <div class="mb-5">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-exclamation-triangle text-danger fs-3 me-3"></i>
                        <div>
                            <h4 class="text-danger mb-1">{{t('Import Errors Found')}}</h4>
                            <p class="text-muted mb-0">{{t('Found')}} <span
                                    class="fw-bold text-danger">{{$file->logErrors->count()}}</span> {{t('errors during import process')}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-sm btn-primary me-2" onclick="expandAllErrors()">
                        <i class="fas fa-expand-alt me-1"></i>{{t('Expand All')}}
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="collapseAllErrors()">
                        <i class="fas fa-compress-alt me-1"></i>{{t('Collapse All')}}
                    </button>
                </div>
            </div>
        </div>

        <!-- Compact Error List -->
        <div class="accordion" id="errorsAccordion">
            @foreach($file->logErrors as $index => $logError)
                <div class="accordion-item mb-2 error-item">
                    <h2 class="accordion-header" id="heading{{$index}}">
                        <button class="accordion-button collapsed error-header" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{$index}}" aria-expanded="false"
                                aria-controls="collapse{{$index}}">
                            <div class="d-flex align-items-center w-100">
                                <div class="me-3">
                                    <span class="badge badge-danger">{{t('Row')}} {{$logError->row_num ?? 'N/A'}}</span>
                                </div>
                                <div class="flex-grow-1">
                                    @if(isset($logError->data['errors']) && is_array($logError->data['errors']) && count($logError->data['errors']) > 0)
                                        <span
                                            class="text-danger fw-bold">{{$logError->data['errors'][0]}}</span>
                                        @if(count($logError->data['errors']) > 1)
                                            <span
                                                class="text-muted ms-2">(+{{count($logError->data['errors']) - 1}} {{t('more errors')}})</span>
                                        @endif
                                        @if($logError->updated)
                                            <span class="badge badge-info">{{t("updated")}}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">{{t('No error details available')}}</span>
                                    @endif
                                </div>
                                <div class="ms-3">
                                    @if(isset($logError->data['inputs']) && is_array($logError->data['inputs']))
                                        <span
                                            class="text-muted small">{{count($logError->data['inputs'])}} {{t('fields')}}</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{$index}}" class="accordion-collapse collapse" aria-labelledby="heading{{$index}}"
                         data-bs-parent="#errorsAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <!-- All Errors Section -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-danger mb-3">
                                        <i class="fas fa-times-circle me-2"></i>{{t('All Errors')}}
                                    </h6>
                                    @if(isset($logError->data['errors']) && is_array($logError->data['errors']))
                                        <div class="error-list">
                                            @foreach($logError->data['errors'] as $error)
                                                <div class="alert alert-danger alert-sm py-2 mb-2">
                                                    <i class="fas fa-exclamation-circle me-2"></i>{{$error}}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-secondary alert-sm py-2">
                                            <i class="fas fa-info-circle me-2"></i>{{t('No error details available')}}
                                        </div>
                                    @endif
                                </div>

                                <!-- Input Data Section -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-database me-2"></i>{{t('Input Data')}}
                                    </h6>
                                    @if(isset($logError->data['inputs']) && is_array($logError->data['inputs']))
                                        <div class="input-data-compact">
                                            @foreach($logError->data['inputs'] as $input)
                                                <div class="d-flex align-items-center py-1 border-bottom border-light">
                                                    <div class="flex-shrink-0 me-3" style="width: 120px;">
                                                        <small
                                                            class="text-muted fw-bold">{{$input['key'] ?? 'N/A'}}</small>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        @if(!empty($input['value']) && $input['value'] !== '000-00-0000' && $input['value'] !== '')
                                                            <small class="text-dark">{{$input['value']}}</small>
                                                        @else
                                                            <small class="text-muted fst-italic">{{t('Empty')}}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-secondary alert-sm py-2">
                                            <i class="fas fa-info-circle me-2"></i>{{t('No input data available')}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-10 no-errors-container">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-success mb-2">{{t('No Errors Found')}}</h4>
            <p class="text-muted">{{t('This import file has no errors. All data was processed successfully.')}}</p>
        </div>
    @endif
@endsection
@section('script')
    <script>
        function expandAllErrors() {
            const accordionButtons = document.querySelectorAll('.accordion-button.collapsed');
            accordionButtons.forEach(button => {
                button.click();
            });
        }

        function collapseAllErrors() {
            const accordionButtons = document.querySelectorAll('.accordion-button:not(.collapsed)');
            accordionButtons.forEach(button => {
                button.click();
            });
        }


    </script>
@endsection
