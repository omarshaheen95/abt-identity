@php
    $lang = app()->getLocale();
@endphp
<!--begin::Header-->
<div id="kt_app_header" class="app-header">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!--begin::Sidebar mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        <!--end::Sidebar mobile toggle-->
        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="#!" class="d-lg-none">
                <img alt="Logo" src="{{!settingCache('logo_min')? asset('logo_min.svg'):asset(settingCache('logo_min'))}}" class="h-30px" />
            </a>
        </div>
        <!--end::Mobile logo-->
        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-end flex-lg-grow-1" id="kt_app_header_wrapper">


            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0">
                <!--begin::Local-->
                <div class="app-navbar-item ms-1 ms-md-3">
                    <!--begin::Menu toggle-->
                        <!--begin::Item-->
                    @if(app()->getLocale() == "ar")
                        <a href="{{ route(getGuard().'.switch-language', 'en') }}" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                           data-kt-menu-placement="{{$lang=='ar'?'bottom-start':'bottom-end'}}">
	                            <span class="symbol symbol-25px">
                                    <img src="{{asset('assets_v1/media/flags/united-states.svg')}}" alt=""/>
                                </span>
                        </a>
                    @else
                        <a href="{{ route(getGuard().'.switch-language', 'ar') }}" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                           data-kt-menu-placement="{{$lang=='ar'?'bottom-start':'bottom-end'}}">
	                            <span class="symbol symbol-25px">
                                  <img src="{{asset('assets_v1/media/flags/united-arab-emirates.svg')}}" alt=""/>
                                </span>
                        </a>
                    @endif

                </div>
                <!--end::Local-->

{{--                @if(in_array(request()->get('guard'),['manager','school','supervisor']))--}}
{{--                    @include('general.layout_parts.notifications')--}}
{{--                @endif--}}

                <!--begin::Theme mode-->
                <div class="app-navbar-item ms-1 ms-md-3">
                    <!--begin::Menu toggle-->
                    <a href="#" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                       data-kt-menu-placement="{{$lang=='ar'?'bottom-start':'bottom-end'}}">
                        <i class="ki-duotone ki-night-day theme-light-show fs-2 fs-lg-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-2 fs-lg-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <!--begin::Menu toggle-->
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
												<span class="menu-icon" data-kt-element="icon">
													<i class="ki-duotone ki-night-day fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
														<span class="path5"></span>
														<span class="path6"></span>
														<span class="path7"></span>
														<span class="path8"></span>
														<span class="path9"></span>
														<span class="path10"></span>
													</i>
												</span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
												<span class="menu-icon" data-kt-element="icon">
													<i class="ki-duotone ki-moon fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
													</i>
												</span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
												<span class="menu-icon" data-kt-element="icon">
													<i class="ki-duotone ki-screen fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
													</i>
												</span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Theme mode-->


                <!--begin::User menu-->
                <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                         data-kt-menu-placement="{{$lang=='ar'?'bottom-start':'bottom-end'}}">
                        @if(isset(Auth::guard()->user()->image))
                            <img src="{{asset(Auth::guard()->user()->image)}}" alt="user" />
                        @elseif(isset(Auth::guard()->user()->logo))
                            <img src="{{asset(Auth::guard()->user()->logo)}}" alt="user" />
                        @else
                            <img src="{{asset('assets_v1/media/svg/avatars/blank.svg')}}" alt="user" />
                        @endif
                    </div>
                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-50px me-5">
                                    @if(isset(Auth::guard()->user()->image))
                                        <img alt="Logo" src="{{asset(Auth::guard()->user()->image)}}"  />
                                    @elseif(isset(Auth::guard()->user()->logo))
                                        <img alt="Logo" src="{{asset(Auth::guard()->user()->logo)}}"  />
                                    @else
                                        <img alt="Logo" src="{{asset('assets_v1/media/svg/avatars/blank.svg')}}"  />
                                    @endif
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Username-->
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name }}
                                </div>
                                    <div>
                                        <a href="#!" class="fw-semibold text-muted text-hover-primary fs-9">{{ Auth::user()->email }}</a>
                                    </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                            <div class="menu-item px-5">
                                <a href="{{route(getGuard().'.edit-profile')}}" class="menu-link px-5">{{t('Profile')}}</a>
                            </div>

                            <div class="menu-item px-5">
                                <a href="{{route(getGuard().'.edit-password')}}" class="menu-link px-5">{{t('Update Password')}}</a>
                            </div>
                            <div class="menu-item px-5">
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <form id="logout-form" action="{{ url('/'.getGuard().'/logout') }}" method="POST"
                                  style="display: none;">
                               @csrf
                            </form>
                            <a href="{{ url('/'.getGuard().'/logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();"
                               class="menu-link px-5">{{t('Sign Out')}}</a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::User account menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User menu-->
                <!--begin::Header menu toggle-->
                <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
                    <div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px" id="kt_app_header_menu_toggle">
                        <i class="ki-duotone ki-element-4 fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <!--end::Header menu toggle-->
            </div>
            <!--end::Navbar-->
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>
</div>
<!--end::Header-->
