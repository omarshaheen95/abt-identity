<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{route('inspection.home')}}">
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
                        <a class="menu-link @if(Request::is('inspection/home*') ) active @endif" href="{{ route('inspection.home') }}">
                        <span class="menu-icon">
                      <i class="ki-duotone ki-chart-pie-simple fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                        </span>
                            <span class="menu-title">{{t('Dashboard')}}</span>
                        </a>
                    </div>



                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('inspection/school*')) active @endif" href="{{ route('inspection.school.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-teacher fs-2">
                         <i class="path1"></i>
                         <i class="path2"></i>
                        </i>
                    </span>
                            <span class="menu-title">{{t('Schools')}}</span>
                        </a>
                    </div>



                    <div class="menu-item">
                        <a class="menu-link @if(Request::is('inspection/student') )active @endif" href="{{ route('inspection.student.index') }}">
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



            </div>



            <!--end::Menu-->




        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->

</div>
<!--end::Sidebar-->
