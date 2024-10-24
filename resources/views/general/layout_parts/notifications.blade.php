@php

if (request()->get('guard') == 'manager'){
    $notifiable_type = \App\Models\Manager::class;
}elseif (request()->get('guard') == 'supervisor'){
        $notifiable_type = \App\Models\Supervisor::class;

}elseif (request()->get('guard') == 'school'){
        $notifiable_type = \App\Models\School::class;

}elseif (request()->get('guard') == 'teacher'){
        $notifiable_type = \App\Models\Teacher::class;
}
    $notifications = \App\Models\Notification::query()->where(function ($query){
            $query->where('notifiable_id', auth()->user()->id)->orWhere('notifiable_id', 0);
        })->where('notifiable_type', $notifiable_type)
            ->latest()->whereNull('read_at')->latest()->get();
@endphp
<!--Begin Notification-->
<div class="app-navbar-item ms-1 ms-md-3">
    <!--begin::Menu- wrapper-->
    <div class="symbol">
        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="{{$lang=='ar'?'bottom-start':'bottom-end'}}" id="kt_menu_item_wow">
            <i class="ki-duotone ki-notification fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </div>
        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="kt_menu_notifications" style="">
            <!--begin::Heading-->
            <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url({{asset('assets_v1/images/menu-header-bg.jpg')}})">
                <!--begin::Title-->
                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">{{t('Notifications')}}</h3>
{{--                href="{{ route(getGuard().'.notification.read_all') }}"--}}
                @if(isset($notifications) && count($notifications))
                    <a class="text-white align-self-end mx-2 mb-3" style="font-size: 8px" >{{t('All as read')}}</a>
                @endif
                <!--end::Title-->
            </div>
            <div class="scroll-y mh-325px my-5 px-8" id="notification_list">
                @isset($notifications)
                    @foreach($notifications as $notification)
                        <!--begin::Item-->
{{--                        href="{{ route(getGuard().'.notification.show', $notification->id) }}"--}}
                        <a  class="fs-6 text-gray-800 text-hover-primary fw-bold">
                            <div class="d-flex flex-stack py-4">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-35px me-4 ">
																<span class="symbol-label bg-light-primary">
																	<i class="ki-duotone ki-notification-bing fs-2">
                                                                     <span class="path1"></span>
                                                                     <span class="path2"></span>
                                                                     <span class="path3"></span>
                                                                    </i>
																</span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div class="mb-0 me-2">
                                        <div class="text-gray-800 fs-7"> {{ $notification->title }}</div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Label-->
                                <span class="badge badge-light fs-8">{{ $notification->created_at->diffForHumans() }}</span>
                                <!--end::Label-->
                            </div>

                        </a>
                        <!--end::Item-->
                    @endforeach
                @endisset

            </div>
        </div>
        <!--end::Menu-->
        <span class="symbol-badge badge badge-circle bg-danger start-100 mt-2 " style="height: 18px;width: 18px;color:#ffffff;font-size: 2px;{{isset($notifications) && count($notifications) > 0 ? '':'display:none;'}}" id="notification_count">{{ isset($notifications)?count($notifications):'' }}</span>
    </div>


    <!--end::Menu wrapper-->
</div>
<!--End Notification-->
