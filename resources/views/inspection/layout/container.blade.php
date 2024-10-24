<!DOCTYPE html>
<html lang="{{app()->getlocale()}}" dir="{{app()->getLocale()=='ar'?'rtl':'ltr'}}">
<!-- begin::Head -->
<head>
    @include('general.layout_parts.style')
</head>

<!-- end::Head -->

<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="light-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<!--begin::Theme mode setup on page load-->
<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
<!--end::Theme mode setup on page load-->
<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
        @include('general.layout_parts.header')
        <!--begin::Wrapper-->
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            @include(getGuard().'.layout.sidebar')

            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid">

                    <!--begin::Toolbar-->
                    <div id="kt_app_toolbar" class="app-toolbar  py-3 py-lg-6 ">

                        <!--begin::Toolbar container-->
                        <div id="kt_app_toolbar_container" class="app-container  {{isset($container_type) ?  $container_type:'container-xxl'}} d-flex flex-stack ">



                            <!--begin::Page title-->
                            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
                                <!--begin::Title-->
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                    @yield('title')
                                </h1>
                                <!--end::Title-->


                                <!--Breadcrumb-->
                                <ol class="breadcrumb breadcrumb-line text-muted fs-7 fw-semibold">
                                    @if(Request::is(getGuard().'/home'))
                                        <li class="breadcrumb-item text-muted">{{t('Home')}}</li>
                                    @else
                                        <li class="breadcrumb-item"><a href="{{route(getGuard().'.home')}}" class=""> {{t('Home')}}</a></li>
                                    @endif
                                    @stack('breadcrumb')
                                </ol>
                                <!--Breadcrumb-->

                            </div>
                            <!--end::Page title-->
                            <!--begin::Actions-->
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                @yield('actions')
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Toolbar container-->
                    </div>
                    <!--end::Toolbar-->

                    <!--begin::Content-->
                    <div id="kt_app_content" class="app-content  flex-column-fluid ">


                        <!--begin::Content container-->
                        <div id="kt_app_content_container" class="app-container  {{isset($container_type) ?  "$container_type":'container-xxl'}} ">
                            @if(Session::has('not_active'))
                                <!--begin::Alert-->
                                <div class="alert alert-warning d-flex align-items-center p-5">
                                    <!--begin::Icon-->
                                    <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                        <i class="path3"></i>
                                    </i>
                                    <!--end::Icon-->

                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Title-->
                                        <h4 class="mb-1 text-dark">{{t('Not Active Account')}}</h4>
                                        <!--end::Title-->

                                        <!--begin::Content-->
                                        <span class="text-gray-800">{{t('Your account is not active, please contact support')}}</span>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Alert-->
                            @else
                                <!--begin::Card-->
                                @yield('charts')
                                @hasSection('pre-content')
                                    @yield('pre-content')
                                @endif
                                @hasSection('content')
                                    <div class="card py-2 px-3">
                                        <!--begin::Card header-->
                                        @if (count($errors) > 0)
                                            <div class="mx-10 mt-5 mb-0 alert alert-dismissible alert-danger d-flex flex-column flex-sm-row">
                                                <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                                    <i class="path1"></i>
                                                    <i class="path2"></i>
                                                    <i class="path3"></i>
                                                </i>
                                                <div class="d-flex flex-column">
                                                    @foreach ($errors->all() as $error)
                                                        <span>{{ $error }}</span>
                                                    @endforeach
                                                </div>
                                                <!--begin::Close-->
                                                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                                    <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                </button>
                                                <!--end::Close-->
                                            </div>
                                        @endif
                                        @hasSection('filter')
                                            <!--begin::Accordion-->

                                            <!--end::Accordion-->

                                            <div class="row mt-5">
                                                <div class="accordion " id="kt_accordion_1">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                                            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_1" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                                                                {{t('Filter')}}
                                                            </button>
                                                        </h2>
                                                        <div id="kt_accordion_1_body_1" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_1">
                                                            <div class="accordion-body">
                                                                <div class="card-header border-0 pt-6">
                                                                    <form class="w-100 filter" id="filter" action="" autocomplete="off">
                                                                        @csrf
                                                                        <div class="row">
                                                                            @yield('filter')

                                                                            <div class="col-12 mt-2 d-flex justify-content-end gap-2">

                                                                                <button type="button" class="btn btn-primary " id="kt_search">
                                                            <span>
                                                                <i class="la la-search"></i>
                                                                <span>{{t('Search')}}</span>
                                                            </span>
                                                                                </button>

                                                                                <button type="reset" class="btn btn-secondary " id="kt_reset">
                                                            <span>
                                                                <i class="la la-close"></i>
                                                                <span>{{t('Reset')}}</span>
                                                            </span>
                                                                                </button>

                                                                                @hasSection('filter-actions')
                                                                                    @yield('filter-actions')
                                                                                @else

                                                                            </div>
                                                                            @endif
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>




                                        @endif
                                        <!--end::Card header-->

                                        <!--begin::Card body-->
                                        <div class="card-body">

                                            @yield('content')

                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                @endif
                                <!--end::Card-->
                            @endif

                        </div>
                        <!--end::Content container-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Content wrapper-->


                @include('general.layout_parts.footer')
            </div>
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::App-->


<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</div>
<!--end::Scrolltop-->


<script>
    let LevelGradesRoute = '{{route('inspection.level.levelGrades')}}'
</script>
@include('general.layout_parts.script')

</body>
<!--end::Body-->
</html>
