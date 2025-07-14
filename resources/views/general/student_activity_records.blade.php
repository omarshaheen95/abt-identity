@extends(getGuard().'.layout.container')

@section('style')
    <style>
        .no-sessions-container {
            background: linear-gradient(135deg, #f8fff8 0%, #f0fdf4 100%);
            border-radius: 1rem;
            border: 1px solid #bbf7d0;
            padding: 4rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .no-sessions-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }

        .session-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .session-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .session-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: none;
            padding: 1.5rem;
            box-shadow: none;
            border-radius: 0.875rem 0.875rem 0 0;
            position: relative;
            overflow: hidden;
        }

        .session-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #06b6d4, #10b981);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .session-header:hover::before {
            opacity: 1;
        }

        .session-header:not(.collapsed) {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 1px solid #3b82f6;
        }

        .session-header:not(.collapsed)::before {
            opacity: 1;
        }

        .session-header:focus {
            box-shadow: none;
            border-color: #3b82f6;
        }

        .search-filters {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        .search-filters .form-label {
            color: #374151;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .search-filters .form-control {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .search-filters .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .badge-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
        }

        .badge-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(6, 182, 212, 0.2);
        }

        .term-data-compact {
            max-height: 400px;
            overflow-y: auto;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .term-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .term-container:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .step-status {
            padding: 0.75rem;
            border-left: 3px solid #dee2e6;
            padding-left: 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
            background: rgba(255, 255, 255, 0.5);
            margin-bottom: 1rem;
            width: calc(50% - 0.5rem);
        }

        .step-status.completed {
            border-left-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .step-status.started {
            border-left-color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
        }

        .student-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .loading-spinner {
            text-align: center;
            padding: 4rem 2rem;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.25em;
        }

        .pagination {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .pagination .page-link {
            border: none;
            padding: 0.75rem 1rem;
            color: #6b7280;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: #f3f4f6;
            color: #3b82f6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .accordion-button::after {
            background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%233b82f6'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
            transform: rotate(-90deg);
            transition: transform 0.3s ease;
        }

        .accordion-button:not(.collapsed)::after {
            transform: rotate(0deg);
        }

        .border-bottom.border-light:last-child {
            border-bottom: none !important;
        }

        .term-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .alert-sm {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }

        .date-badge {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #d1d5db;
        }

        .session-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .session-meta .badge {
            white-space: nowrap;
        }

        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .summary-icon {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
        }

        .btn-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(6, 182, 212, 0.2);
        }

        .btn-info:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
        }


        .status-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            margin-right: 0.5rem;
        }

        .status-icon.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-icon.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .status-icon.info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .search-filters {
                padding: 1.5rem;
            }

            .session-header {
                padding: 1rem;
            }

            .student-info-card {
                padding: 1.5rem;
            }

            .term-container {
                padding: 1rem;
            }

            .session-meta {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        @media (max-width: 991.98px) {
            .step-status {
                width: 100% !important;
            }
        }
    </style>
@endsection

@section('title')
    {{$title}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route(getGuard().'.student.index')}}" class="text-muted">
            {{t('Students')}}
        </a>
    </li>
    @if(isset($student))
        <li class="breadcrumb-item text-muted">
            <a href="{{route(getGuard().'.student.edit', $student->id)}}" class="text-muted">
                {{$student->name}}
            </a>
        </li>
    @endif
@endpush

@section('filter')
    <div class="row">
        <div class="col-4 mb-2">
            <label class="mb-1">{{t(' Date')}}:</label>
            <input type="text" name="history_date" id="history_date" class="form-control form-control-solid" placeholder="{{t('Date')}}" />
            <input type="hidden" name="start_date" id="start_history_date" />
            <input type="hidden" name="end_date" id="end_history_date" />
        </div>
    </div>
@endsection

@section('content')
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{t('Loading...')}}</span>
        </div>
        <p class="mt-3 text-muted">{{t('Loading Student Activity Records...')}}</p>
    </div>

    <!-- Sessions List -->
    <div class="accordion" id="sessionsAccordion" style="display: none;">
        <!-- Sessions will be loaded here -->
    </div>

    <!-- Summary Statistics -->
    <div class="mb-5" id="summary-section" style="display: none;">
        <div class="summary-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted mb-0">{{t('Found')}} <span class="fw-bold text-primary" id="total-sessions">0</span> {{t('Student Activity Records with submitted assessments')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- No Sessions Found -->
    <div class="text-center no-sessions-container" id="no-sessions" style="display: none;">
        <div class="mb-4">
            <i class="fas fa-search text-info" style="font-size: 4rem;"></i>
        </div>
        <h4 class="text-info mb-3">{{t('No Student Activity Records Found')}}</h4>
        <p class="text-black">{{t('No student activity records match your search criteria. Try adjusting your filters.')}}</p>
    </div>

    <!-- Pagination -->
    <div class="pagination-container" id="pagination-container" style="display: none;">
        <!-- Pagination will be loaded here -->
    </div>
@endsection

<script src="{{asset('assets_v1/js/manager/models/general.js')}}"></script>

@section('script')
    <script>
        initializeDateRangePicker('history_date')

        let currentPage = 1;
        const perPage = 20;
        const studentId = @json($studentId ?? null);

        // Date formatting function
        function formatDate(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function formatDateShort(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        $(document).ready(function() {
            loadSessions();
        });

        function loadSessions(page = 1) {
            $('#loading-spinner').show();
            $('#sessionsAccordion').hide();
            $('#no-sessions').hide();
            $('#summary-section').hide();
            $('#pagination-container').hide();

            const formData = {
                page: page,
                per_page: perPage
            };

            // Always include date filters
            formData.start_date = $('#start_history_date').val();
            formData.end_date = $('#end_history_date').val();

            // Build URL based on whether viewing specific student or not
            let url = "{{ route(getGuard().'.student.activity-records',':id') }}".replace(':id',studentId)

            $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                success: function(response) {
                    $('#loading-spinner').hide();

                    if (response.data && response.data.length > 0) {
                        renderSessions(response.data);
                        $('#total-sessions').text(response.total || response.data.length);
                        $('#summary-section').show();
                        $('#sessionsAccordion').show();

                        if (response.total > perPage) {
                            renderPagination(response);
                        }
                    } else {
                        $('#no-sessions').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading-spinner').hide();
                    console.error('Error loading sessions:', error);
                    alert('{{t("Error loading recoreds. Please try again.")}}');
                }
            });
        }

        function renderSessions(sessions) {
            let html = '';

            sessions.forEach((session, index) => {
                const loginSession = session.login_session;
                const studentTerms = session.login_session.student_terms;
                const loginDate = session.login_date;

                // For specific student view, don't show student info in header
                let headerContent = `
                    <div class="d-flex gap-2 w-100">
                        <div class="date-badge">
                                <i class="fas fa-calendar-alt me-2"></i>
                                ${formatDate(loginSession.created_at)}
                            </div>
                            <div class="badge badge-info">
                                <i class="fas fa-clipboard-list me-1 text-white"></i>
                                ${studentTerms.length} ${studentTerms.length === 1 ? '{{t("assessment")}}' : '{{t("assessments")}}'}
                            </div>
                    </div>
                `;

                // Format the session data for display
                let sessionDataHtml = '';
                if (loginSession.data) {
                    sessionDataHtml = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <strong>{{t('IP Address')}}:</strong>
                            <span class="ms-2">${loginSession.data.ip || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-browser text-info me-2"></i>
                            <strong>{{t('Browser')}}:</strong>
                            <span class="ms-2">${loginSession.data.browser || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-code-branch text-success me-2"></i>
                            <strong>{{t('Version')}}:</strong>
                            <span class="ms-2">${loginSession.data.browser_version || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-user-agent text-warning me-2 mt-1"></i>
                            <div>
                                <strong>{{t('User Agent')}}:</strong>
                                <div class="text-muted small mt-1" style="word-break: break-all;">
                                    ${loginSession.data.user_agent || 'N/A'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                }

                html += `
    <div class="accordion-item mb-3 session-item">
        <h2 class="accordion-header" id="heading${index}">
            <button class="accordion-button collapsed session-header" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                ${headerContent}
            </button>
        </h2>
        <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}"
             data-bs-parent="#sessionsAccordion">
            <div class="accordion-body">
                <div class="row">
                <div class="col-md-12 mb-3">
                        <div class="alert alert-dismissible bg-light-primary border border-primary d-flex flex-column flex-sm-row p-5 mb-10">
                            <i class="ki-duotone ki-abstract-4 fs-2hx text-success me-4 mb-5 mb-sm-0">
                             <span class="path1"></span>
                             <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column pe-0 pe-sm-10 w-100">
                                <h5 class="mb-3">{{t('Student Activity Data')}}</h5>
                                ${sessionDataHtml}
                            </div>
                        </div>
                    </div>
                 <!-- Submitted Terms -->
                    <div class="col-md-12 mb-3">
                        <h6 class="text-gradient mb-5">
                            <i class="fas fa-clipboard-check me-2 text-info"></i>${'{{t("Submitted Assessments")}}'}
                        </h6>
                        ${renderTerms(studentTerms, loginDate)}
                    </div>
                </div>
             </div>
        </div>
    </div>
`;
            });

            $('#sessionsAccordion').html(html);
        }
        function renderTerms(studentTerms, loginDate) {
            if (!studentTerms || studentTerms.length === 0) {
                return `<div class="alert alert-secondary alert-sm py-3">
            <div class="d-flex align-items-center">
                <div class="status-icon info">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <span class="text-black">${'{{t("No assessments found for this login session")}}'}</span>
            </div>
        </div>`;
            }

            let html = '<div class="term-list">';
            let hasTermsWithSteps = false;

            studentTerms.forEach(studentTerm => {
                const term = studentTerm.term;
                let stepsOnLoginDate = studentTerm.steps_on_login_date || [];

                // Only show term if it has steps started on the login date
                if (studentTerm.has_steps_on_login_date && stepsOnLoginDate.length > 0) {
                    hasTermsWithSteps = true;

                    html += `
                <div class="term-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt text-primary me-2 fs-5"></i>
                            <strong class="text-primary fs-6">${'{{t("Assessment")}}'}: ${term.name}</strong>
                        </div>
                        <div class="badge badge-info">{{t('Round')}}: ${term.round}</div>
                    </div>
                       <div class="d-flex flex-wrap justify-content-between border border-primary border-dashed rounded-1 p-5 mx-4">

            `;
                    // Show steps that were started on the login date
                    stepsOnLoginDate.forEach(stepData => {
                        const statusClass = stepData.is_completed ? 'completed' : 'started';

                        html += `
                    <div class="step-status ${statusClass} mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="status-icon info">
                                <i class="fas fa-play text-white"></i>
                            </div>
                            <small class="text-info fw-bold">{{t('Started At')}}:</small>
                            <span class="ms-2 small text-black">${formatDate(stepData.started_at)}</span>
                        </div>
                `;

                        if (stepData.is_completed && stepData.submitted_at) {
                            html += `
                        <div class="d-flex align-items-center gap-2">
                            <div class="status-icon success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <small class="text-success fw-bold">{{t('Submitted At')}} :</small>
                            <span class="ms-2 small text-black">${formatDate(stepData.submitted_at)}</span>
                        </div>
                    `;
                        } else {
                            html += `
                        <div class="d-flex align-items-center gap-2">
                            <div class="status-icon warning">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <small class="text-warning fw-bold">{{t('Not Submitted')}}</small>
                        </div>
                    `;
                        }

                        html += '</div>';
                    });

                    // Show correction status
                    html += '</div><div class="my-3 pb-2 border-bottom d-flex align-items-center justify-content-between">';
                    if (studentTerm.corrected) {
                        html += `
                    <div class="d-flex align-items-center ms-4">
                        <span class="badge badge-success me-2">
                            <i class="fas fa-check me-1 text-white"></i> ${'{{t("Corrected")}}'}
                        </span>
                        ${studentTerm.corrected_at ? `<small class="text-muted">at ${formatDate(studentTerm.corrected_at)}</small>` : ''}
                    </div>`;
                    } else {
                        html += `
                    <span class="badge badge-warning ms-4">
                        <i class="fas fa-clock me-1 text-white"></i> ${'{{t("Not Corrected")}}'}
                    </span>`;
                    }
                    html += '</div>';

                    html += '</div>';
                }
            });

            html += '</div>';

            // If no terms have steps on the login date, show appropriate message
            if (!hasTermsWithSteps) {
                return `<div class="alert alert-info alert-sm py-3">
            <div class="d-flex align-items-center">
                <div class="status-icon info">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <span class="text-black">${'{{t("No assessments were started on this login date")}}'}</span>
            </div>
        </div>`;
            }

            return html;
        }

        function renderPagination(response) {
            const totalPages = Math.ceil(response.total / perPage);
            const currentPage = response.current_page || 1;

            let html = '<nav aria-label="Page navigation"><ul class="pagination">';

            // Previous button
            if (currentPage > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadSessions(${currentPage - 1})">
                        <i class="fas fa-chevron-left me-1"></i>${'{{t("Previous")}}'}
                    </a>
                </li>`;
            }

            // Page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `<li class="page-item ${activeClass}">
                    <a class="page-link" href="#" onclick="loadSessions(${i})">${i}</a>
                </li>`;
            }

            // Next button
            if (currentPage < totalPages) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadSessions(${currentPage + 1})">
                        ${'{{t("Next")}}'}<i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </li>`;
            }

            html += '</ul></nav>';

            $('#pagination-container').html(html).show();
        }

        // Search functionality
        $('#kt_search').on('click', function() {
            currentPage = 1;
            loadSessions(1);
        });

        // Clear functionality
        $('#kt_reset').on('click', function() {
            $('#start_history_date').val('');
            $('#end_history_date').val('');
            $('#filter')[0].reset();
            currentPage = 1;
            loadSessions(1);
        });

        // // Auto search on date change
        // $('#start_date, #end_date, #delegate_id, #year').on('change', function() {
        //     currentPage = 1;
        //     loadSessions(1);
        // });
    </script>
@endsection
