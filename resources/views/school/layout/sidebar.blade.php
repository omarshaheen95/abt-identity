<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{route('school.home')}}">
            <img alt="Logo" src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" class="h-50px app-sidebar-logo-default p-1" />
            <img alt="Logo" src="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" class="h-30px app-sidebar-logo-minimize" />
        </a>

        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-double-left fs-2 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">


                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('school/home*') ) active @endif" href="{{ route('school.home') }}">
                        <span class="menu-icon">
                      <i class="ki-duotone ki-chart-pie-simple fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                        </span>
                            <span class="menu-title">{{t('Home')}}</span>
                        </a>
                    </div>

                <div class="menu-item">
                    <a class="menu-link @if(Request::is('school/scheduling*'))active @endif" href="{{ route('school.scheduling.index') }}">
                                <span class="menu-icon">
                                   <i class="ki-duotone ki-profile-user fs-2">
                                     <i class="path1"></i>
                                     <i class="path2"></i>
                                     <i class="path3"></i>
                                     <i class="path4"></i>
                                    </i>
                                </span>
                        <span class="menu-title">{{t('Assessments Scheduling')}}</span>
                    </a>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{Request::is('school/student_term*')?'here show':''}}">
                                           <span class="menu-link">
                                                <span class="menu-icon">
                                                <i class="ki-duotone ki-note-2 fs-2">
                                                 <i class="path1"></i>
                                                 <i class="path2"></i>
                                                 <i class="path3"></i>
                                                 <i class="path4"></i>
                                                </i>
                                            </span>
											<span class="menu-title">{{t('Students Assessments')}}</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">

                            <div class="menu-item">
                                <a class="menu-link @if(Request::is('school/student_term/corrected') )active @endif" href="{{ route('school.students-terms',['type'=>'corrected']) }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                    <span class="menu-title">{{t('Corrected Assessments')}}</span>
                                </a>
                            </div>


                        <div class="menu-item">
                                <a class="menu-link @if(Request::is('school/student_term/uncorrected') )active @endif" href="{{ route('school.students-terms',['type'=>'uncorrected']) }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                    <span class="menu-title">{{t('Uncorrected Assessments')}}</span>
                                </a>
                            </div>
                    </div>
                    <!--end:Menu sub-->
                </div>

                <div class="menu-item">
                    <a class="menu-link @if(Request::is('school/students_not_submitted_terms') )active @endif" href="{{ route('school.term.students-not-submitted-terms') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-people fs-2">
                             <i class="path1"></i>
                             <i class="path2"></i>
                             <i class="path3"></i>
                             <i class="path4"></i>
                             <i class="path5"></i>
                            </i>
                        </span>
                        <span class="menu-title">{{t('Not Started Yet ')}}</span>
                    </a>
                </div>



                <div class="menu-item">
                    <a href="{{route('school.marking_requests.index')}}" class="menu-link {{Request::is('school/marking_requests*')?'active':''}}">
                        <span class="menu-icon">
                           <i class="ki-duotone ki-questionnaire-tablet fs-2">
                                 <i class="path1"></i>
                                 <i class="path2"></i>
                                </i>
                        </span>
                        <span class="menu-title">{{t('Marking Requests')}}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link @if(Request::is('school/student')||Request::is('school/student/*'))active @endif" href="{{ route('school.student.index') }}">
                                <span class="menu-icon">
                                   <i class="ki-duotone ki-profile-user fs-2">
                                     <i class="path1"></i>
                                     <i class="path2"></i>
                                     <i class="path3"></i>
                                     <i class="path4"></i>
                                    </i>
                                </span>
                        <span class="menu-title">{{t('Students & Marks')}}</span>
                    </a>
                </div>
                <!--Start Reports-->
                <div class="menu-item">
                    <a class="menu-link link @if(!Auth::user()->allow_reports) menu-item-disabled @endif @if(Request::is('school/pre-student-mark-report*') )active @endif"
                       @if(Auth::user()->allow_reports) href="{{route('school.report.pre-student-mark-report') }}"
                       @else href=""@endif>
                    <span class="menu-icon">
                        <i class="ki-duotone ki-graph-2 fs-2x">
                         <span class="path1"></span>
                         <span class="path2"></span>
                         <span class="path3"></span>
                        </i>
                    </span>
                        <span class="menu-title">{{t('Students Marks')}}</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link @if(!Auth::user()->allow_reports) menu-item-disabled @endif @if(Request::is('school/pre-attainment-report*') )active @endif"
                       @if(Auth::user()->allow_reports)    href="{{route('school.report.pre-attainment-report')}}"
                       @else href=""@endif>
                                <span class="menu-icon">
                        <i class="ki-duotone ki-graph-3 fs-2x">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                    </span>
                        <span class="menu-title">{{t('Attainment Reports')}}</span>
                    </a>
                </div>

                <div data-kt-menu-trigger="click" class="menu-item @if(!Auth::user()->allow_reports) menu-item-disabled @endif menu-accordion
                  {{Request::is('school/pre-progress-report*')||
                  Request::is('school/pre-year-to-year-report*')||
                  Request::is('school/pre-trends-over-time-report*')?'here show':''}}">
                                           <span class="menu-link">
                                                <span class="menu-icon">
                                                <i class="ki-duotone ki-book-open fs-2x">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                        <i class="path3"></i>
                                        <i class="path4"></i>
                                    </i>
                                            </span>
											<span class="menu-title">{{t('The Progress Report')}}</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link @if(!Auth::user()->allow_reports) menu-item-disabled @endif @if(Request::is('school/pre-progress-report*') )active @endif"
                               @if(Auth::user()->allow_reports)    href="{{route('school.report.pre-progress-report')}}"
                               @else href=""@endif>
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">{{t('Within The Academic Year')}}</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link @if(!Auth::user()->allow_reports) menu-item-disabled @endif @if(Request::is('school/pre-year-to-year-report*') )active @endif"
                               @if(Auth::user()->allow_reports)    href="{{route('school.report.pre-year-to-year-report')}}"
                               @else href=""@endif>
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">{{t('Year To Year')}}</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link @if(!Auth::user()->allow_reports) menu-item-disabled @endif @if(Request::is('school/pre-trends-over-time-report*') )active @endif"
                               @if(Auth::user()->allow_reports) href="{{route('school.report.pre-trends-over-time-report')}}" @else href="" @endif>
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">{{t('Trends Over Time')}}</span>
                            </a>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--End Reports-->

            </div>



            <!--end::Menu-->




        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->

</div>
<!--end::Sidebar-->
