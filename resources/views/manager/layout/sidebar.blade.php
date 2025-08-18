<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{route('manager.home')}}">
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
                        <a class="menu-link @if(Request::is('manager/home*') ) active @endif" href="{{ route('manager.home') }}">
                        <span class="menu-icon">
                      <i class="ki-duotone ki-chart-pie-simple fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                        </span>
                            <span class="menu-title">{{t('Dashboard')}}</span>
                        </a>
                    </div>


                @can('show managers')
                        <div class="menu-item">
                            <a class="menu-link @if(Request::is('manager/manager*') ) active @endif" href="{{ route('manager.manager.index') }}">
                        <span class="menu-icon">
                           <i class="ki-duotone ki-briefcase fs-2">
                             <i class="path1"></i>
                             <i class="path2"></i>
                            </i>
                        </span>
                                <span class="menu-title">{{t('Managers')}}</span>
                            </a>
                        </div>
                @endcan


                @can('show schools')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/school*') || Request::is('manager/scheduling/*') && !Request::is('manager/school_level') ) active @endif" href="{{ route('manager.school.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-teacher fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('Schools')}}</span>
                        </a>
                    </div>
                @endcan


                @can('show inspections')
                        <div class="menu-item">
                            <a class="menu-link @if(Request::is('manager/inspection*') ) active @endif" href="{{ route('manager.inspection.index') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-user-tick fs-2">
                                     <i class="path1"></i>
                                     <i class="path2"></i>
                                     <i class="path3"></i>
                                    </i>
                        </span>
                                <span class="menu-title">{{t('Inspection')}}</span>
                            </a>
                        </div>
                @endcan


                @can('show students')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/student')|| Request::is('manager/student/*') )active @endif" href="{{ route('manager.student.index') }}">
                    <span class="menu-icon">
                       <i class="ki-duotone ki-profile-user fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                         <i class="path3"></i>
                         <i class="path4"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('Students')}}</span>
                        </a>
                    </div>
                @endcan


                @can('show levels')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/level*') )active @endif" href="{{ route('manager.level.index') }}">
                    <span class="menu-icon">
                       <i class="ki-duotone ki-ranking fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                         <i class="path3"></i>
                         <i class="path4"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('Levels')}}</span>
                        </a>
                    </div>
                @endcan

                @can('show abt grouping students')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/abt_students*') )active @endif"
                           href="{{ route('manager.abt-id.abt_students') }}">
                    <span class="menu-icon">
                       <i class="ki-duotone ki-user-edit fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                         <i class="path3"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('ABT Grouping Students')}}</span>
                        </a>
                    </div>
                @endcan

                @if(Auth::guard('manager')->user()->hasAnyDirectPermission(['show terms','show terms questions','show questions standards']))

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion
                     {{Request::is('manager/term')||
                       Request::is('manager/term/*/edit')||
                       Request::is('manager/term/*/questions_structure')||
                       Request::is('manager/term/*/questions')||
                       Request::is('manager/terms_questions')||
                       Request::is('manager/standards')||
                       Request::is('manager/question-file*')?'here show':''
                 }}">
                                           <span class="menu-link">
                                                <span class="menu-icon">
                                                <i class="ki-duotone ki-note-2 fs-2">
                                                 <i class="path1"></i>
                                                 <i class="path2"></i>
                                                 <i class="path3"></i>
                                                 <i class="path4"></i>
                                                </i>
                                            </span>
											<span class="menu-title">{{t('Assessments')}}</span>
											<span class="menu-arrow"></span>
										</span>
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            @can('show terms')
                                <div class="menu-item">
                                    <a class="menu-link
                                    @if(Request::is('manager/term')||
                                       Request::is('manager/term/*/edit')||
                                       Request::is('manager/term/*/questions_structure')||
                                       Request::is('manager/term/*/questions')
                                            )active
                        @endif" href="{{ route('manager.term.index') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Assessments')}}</span>
                                    </a>
                                </div>
                            @endcan

                            @can('show terms questions')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/terms_questions') )active @endif" href="{{ route('manager.term.terms-questions') }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                        <span class="menu-title">{{t('Assessments Questions')}}</span>
                                    </a>
                                </div>
                            @endcan

                            @can('show questions standards')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/standards') )active @endif" href="{{ route('manager.term.standards') }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                        <span class="menu-title">{{t('Questions Standards')}}</span>
                                    </a>
                                </div>
                            @endcan
                            @can('show imported questions')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/question-file*') )active @endif" href="{{ route('manager.question-file.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">{{t('Import Questions')}}</span>
                                    </a>
                                </div>
                            @endcan


                        </div>
                        <!--end:Menu sub-->
                    </div>
                @endif



                @if(Auth::guard('manager')->user()->hasAnyDirectPermission(['show students terms','show students not submitted term']))
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion
                {{Request::is('manager/student_term*') ||Request::is('manager/students_not_submitted_terms') ?'here show':''}}">
                                           <span class="menu-link">
                                                <span class="menu-icon">
                                               <i class="ki-duotone ki-book-open fs-2">
                                                     <i class="path1"></i>
                                                     <i class="path2"></i>
                                                     <i class="path3"></i>
                                                     <i class="path4"></i>
                                                </i>
                                            </span>
											<span class="menu-title">{{t('Student Assessments')}}</span>
											<span class="menu-arrow"></span>
										</span>
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">

                            @can('show students terms')

                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/student_term/corrected'))active @endif" href="{{ route('manager.student_term.index',['status'=>'corrected']) }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Corrected Assessments')}}</span>
                                    </a>
                                </div>


                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/student_term/uncorrected'))active @endif" href="{{ route('manager.student_term.index',['status'=>'uncorrected']) }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Uncorrected Assessments')}}</span>
                                    </a>
                                </div>
                            @endcan

                            @can('show students not submitted term')

                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/students_not_submitted_terms') )active @endif" href="{{ route('manager.term.students-not-submitted-terms') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Not Started Yet')}}</span>
                                    </a>
                                </div>
                            @endcan

                        </div>
                        <!--end:Menu sub-->
                    </div>
                @endif
                @can('show marking requests')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/marking_request*') )active @endif" href="{{ route('manager.marking_requests.index') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-questionnaire-tablet fs-2">
                             <i class="path1"></i>
                             <i class="path2"></i>
                            </i>
                        </span>
                            <span class="menu-title">{{t('Marking Requests')}}</span>
                        </a>
                    </div>
                @endcan

                @can('show translation')
                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('manager/text_translation') )active @endif" href="{{ route('manager.text_translation.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-document fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('Text Translation')}}</span>
                        </a>
                    </div>
                @endcan


{{--                'show years','show activity logs','show students import'--}}
                @if(Auth::guard('manager')->user()->hasAnyDirectPermission(['show settings', 'copy terms','show years', 'show activity logs', 'show students import', 'show login sessions', 'show upgrade terms']))
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{Request::is('manager/settings*')||Request::is('manager/copy_term*')||Request::is('manager/upgrade_student_term*')  || Request::is('manager/year*')|| Request::is('manager/activity-log')|| Request::is('manager/login_sessions*')||Request::is('manager/students_files_import*')?'here show':''
                 }}  ">
                    <span class="menu-link">
                                            <span class="menu-icon">
                                                <i class="ki-duotone ki-setting-3 fs-2">
                                             <i class="path1"></i>
                                             <i class="path2"></i>
                                             <i class="path3"></i>
                                             <i class="path4"></i>
                                             <i class="path5"></i>
                                            </i>
                                            </span>
											<span class="menu-title">{{t('Settings')}}</span>
											<span class="menu-arrow"></span>
										</span>
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            @can('show settings')
                            <div class="menu-item">
                                <a class="menu-link @if(Request::is('manager/settings*') )active @endif" href="{{ route('manager.settings.general') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                    <span class="menu-title">{{t('General Settings')}}</span>
                                </a>
                            </div>
                            @endcan
{{--                            @can('show years')--}}
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/year*') )active @endif" href="{{ route('manager.year.index') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Years')}}</span>
                                    </a>
                                </div>
{{--                            @endcan--}}

{{--                            @can('show activity logs')--}}
{{--                            {{ route('manager.activity-log.index') }}--}}
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/activity-log') )active @endif" href="{{route('manager.activity-log.index')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Activity Log')}}</span>
                                    </a>
                                </div>
{{--                            @endcan--}}

{{--                            @can('show students import')--}}
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/students_files_import*') )active @endif" href="{{ route('manager.students_files_import.index') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Student Files')}}</span>
                                    </a>
                                </div>
{{--                            @endcan--}}

                            @can('show upgrade terms')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/upgrade_student_term') )active @endif" href="{{ route('manager.students-terms.view-upgrade-student-term') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Upgrade Downgrade Students Terms')}}</span>
                                    </a>
                                </div>
                            @endcan
                            @can('show login sessions')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/login_sessions*') )active @endif" href="{{ route('manager.login_sessions.index') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Login Sessions')}}</span>
                                    </a>
                                </div>
                            @endcan

                            @can('copy terms')
                                <div class="menu-item">
                                    <a class="menu-link @if(Request::is('manager/copy_term*') )active @endif" href="{{ route('manager.term.copy_term_view') }}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">{{t('Copy Assessments')}}</span>
                                    </a>
                                </div>
                            @endcan


                        </div>
                        <!--end:Menu sub-->
                    </div>
                @endif

            </div>



            <!--end::Menu-->




        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->

</div>
<!--end::Sidebar-->
